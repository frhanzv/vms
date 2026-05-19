package com.safeg.activities

import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.view.View
import android.widget.ImageView
import androidx.appcompat.app.AppCompatActivity
import com.androidnetworking.AndroidNetworking
import com.androidnetworking.common.Priority
import com.androidnetworking.error.ANError
import com.androidnetworking.interfaces.JSONObjectRequestListener
import com.awesomedialog.blennersilva.awesomedialoglibrary.AwesomeProgressDialog
import com.journeyapps.barcodescanner.DecoratedBarcodeView
import com.safeg.Constants
import com.safeg.R
import com.safeg.StaticData
import com.safeg.dispenser.K720Manager
import com.safeg.utils.Common
import com.safeg.utils.SslUtils
import org.json.JSONObject

class CollectCardActivity : AppCompatActivity() {

    private lateinit var barcodeView: DecoratedBarcodeView
    private var isProcessing = false
    private lateinit var k720Manager: K720Manager

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_qr_scan_custom)
        val title = intent.getStringExtra("title") ?: "Scan Visitor Card QR Code"
        findViewById<android.widget.TextView>(R.id.tvTitle).text = title

        barcodeView = findViewById(R.id.barcode_scanner)
        barcodeView.visibility = View.VISIBLE

        findViewById<ImageView>(R.id.ivBack).setOnClickListener {
            setResult(RESULT_CANCELED)
            finish()
        }

        try {
            System.loadLibrary("ttceserial_port")
        } catch (e: UnsatisfiedLinkError) {}

        k720Manager = K720Manager(this)

        Thread {
            try {
                val connected = k720Manager.connect()
                if (connected) {
                    runOnUiThread {
                        Common.showToast(applicationContext, "Dispensing Card...Kindly Collect Card...", Common.ToastType.INFO)
                    }
                    k720Manager.sendCard()
                } else {
                    runOnUiThread {
                        Common.showToast(applicationContext, "Card Dispenser Not Connected", Common.ToastType.WARNING)
                    }
                }
            } catch (e: Exception) {
                runOnUiThread {
                    Common.showToast(applicationContext, "Card Dispenser Error: ${e.message}", Common.ToastType.ERROR)
                }
            }
        }.start()

        barcodeView.decodeContinuous { result ->
            val scanned = result?.text?.trim() ?: return@decodeContinuous
            if (scanned.isNotEmpty() && !isProcessing) {
                isProcessing = true
                runOnUiThread { processCard(scanned) }
            }
        }
    }

    private fun processCard(data: String) {
        val pDialog = AwesomeProgressDialog(this).apply {
            setCancelable(false)
            setTitle("Please wait")
            setMessage("")
            setColoredCircle(R.color.pherosi)
            show()
        }

        Thread {
            AndroidNetworking.initialize(applicationContext, SslUtils.trustAllClient())
            runOnUiThread {
                AndroidNetworking.post(Constants.decrypt)
                    .setTag(Constants.decrypt)
                    .setPriority(Priority.HIGH)
                    .addJSONObjectBody(
                        // ✅ Controller reads "data" field only — removed "ciphertext" and "secret"
                        JSONObject().put("data", data)
                    )
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            // ✅ Controller returns { "status": "success", "data": "plaintext" }
                            val decryptedValue = response.optString("data", "")
                            Log.d("DECRYPT", "decrypted: $decryptedValue")

                            if (decryptedValue.isBlank()) {
                                pDialog.hide()
                                Common.showToast(applicationContext, "Invalid QR Code — Please Scan Again", Common.ToastType.ERROR)
                                isProcessing = false
                                return
                            }

                            val photoBase64 = if (StaticData.base64_face.isNotBlank())
                                "data:image/png;base64,${StaticData.base64_face}"
                            else ""

                            // ✅ insertVendorPassCard requires invitationId — no icNo fallback
                            // VVIP flow: invitationId must be set before reaching here
                            val requestBody = if (StaticData.invitationId > 0) {
                                JSONObject()
                                    .put("cardId", decryptedValue)
                                    .put("invitationId", StaticData.invitationId)
                            } else {
                                // ✅ invitationId not set — show error, cannot assign card
                                pDialog.hide()
                                Log.e("COLLECT_CARD", "invitationId not set — cannot assign card")
                                Common.showToast(
                                    applicationContext,
                                    "Registration not completed — Please restart the process",
                                    Common.ToastType.ERROR
                                )
                                isProcessing = false
                                return
                            }

                            AndroidNetworking.post(Constants.insertVendorPassCard)
                                .setTag(Constants.insertVendorPassCard)
                                .setPriority(Priority.HIGH)
                                .addJSONObjectBody(requestBody)
                                .build()
                                .getAsJSONObject(object : JSONObjectRequestListener {
                                    override fun onResponse(insertResponse: JSONObject) {
                                        pDialog.hide()
                                        Log.d("INSERT_CARD", insertResponse.toString())

                                        // ✅ Controller returns { "status": "success", ... }
                                        val isSuccess = insertResponse.optString("status", "")
                                            .equals("success", ignoreCase = true)

                                        if (isSuccess) {
                                            if (StaticData.isVvip) {
                                                Common.showToast(applicationContext, "VVIP Card Assigned Successfully", Common.ToastType.SUCCESS)
                                                finish()
                                                startActivity(Intent(this@CollectCardActivity, VvipThankYouActivity::class.java))
                                            } else {
                                                Common.showToast(applicationContext, "Card Assigned Successfully", Common.ToastType.SUCCESS)
                                                finish()
                                                val intent = Intent(this@CollectCardActivity, ThankYouActivity::class.java)
                                                intent.putExtra(
                                                    ThankYouActivity.EXTRA_FLOW,
                                                    if (StaticData.invitation) ThankYouActivity.FLOW_INVITATION
                                                    else ThankYouActivity.FLOW_COLLECT_CARD
                                                )
                                                startActivity(intent)
                                            }
                                        } else {
                                            // ✅ Parse error from controller error format
                                            val msg = insertResponse.optString("message", "").ifBlank {
                                                insertResponse.optJSONObject("error")
                                                    ?.optJSONObject("messages")
                                                    ?.optString("0", "") ?: ""
                                            }
                                            val displayMsg = when {
                                                msg.contains("in_use", ignoreCase = true) || msg.contains("not available", ignoreCase = true) -> "Card Already In Use"
                                                msg.contains("not found", ignoreCase = true) -> "Card Not Found"
                                                msg.contains("expired", ignoreCase = true) -> "Card Expired — Please Try Again"
                                                msg.contains("invalid", ignoreCase = true) -> "Invalid Card"
                                                msg.isNotBlank() -> msg
                                                else -> "Failed to Assign Card"
                                            }
                                            Common.showToast(applicationContext, displayMsg, Common.ToastType.ERROR)
                                            isProcessing = false
                                        }
                                    }

                                    override fun onError(anError: ANError) {
                                        pDialog.hide()
                                        Log.e("INSERT_CARD_ERROR", "code=${anError.errorCode} body=${anError.errorBody}")
                                        // ✅ Controller returns HTTP 403 for in_use/lost, 404 for not found
                                        val displayMsg = when (anError.errorCode) {
                                            403 -> "Card Not Available — Already In Use"
                                            404 -> "Card Not Found — Please Try Another Card"
                                            else -> "Network Error — Please Try Again"
                                        }
                                        Common.showToast(applicationContext, displayMsg, Common.ToastType.ERROR)
                                        isProcessing = false
                                    }
                                })
                        }

                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Log.e("DECRYPT_ERROR", "code=${anError.errorCode} body=${anError.errorBody}")
                            Common.showToast(applicationContext, "Invalid QR Code — Please Scan Again", Common.ToastType.ERROR)
                            isProcessing = false
                        }
                    })
            }
        }.start()
    }

    override fun onResume() {
        super.onResume()
        barcodeView.resume()
    }

    override fun onPause() {
        super.onPause()
        barcodeView.pause()
    }
}