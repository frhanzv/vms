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
import com.safeg.models.GetStaffPassByStaffNoOrNameResponseItem
import com.safeg.utils.Common
import com.safeg.utils.SslUtils
import org.json.JSONObject

class PortraitCaptureActivity : AppCompatActivity() {

    private lateinit var barcodeView: DecoratedBarcodeView
    private var isProcessing = false
    private lateinit var k720Manager: K720Manager

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        // ✅ Use camera QR layout
        setContentView(R.layout.activity_qr_scan_custom)

        barcodeView = findViewById(R.id.barcode_scanner)
        // ✅ Show camera view
        barcodeView.visibility = View.VISIBLE

        findViewById<ImageView>(R.id.ivBack).setOnClickListener {
            setResult(RESULT_CANCELED)
            finish()
        }

        try {
            System.loadLibrary("ttceserial_port")
            Log.d("K720", "✅ Native lib loaded")
        } catch (e: UnsatisfiedLinkError) {
            Log.e("K720", "❌ Missing lib: ${e.message}")
        }

        k720Manager = K720Manager(this)

        // ✅ Camera continuous decode
        barcodeView.decodeContinuous { result ->
            val scanned = result?.text?.trim() ?: return@decodeContinuous
            if (scanned.isNotEmpty() && !isProcessing) {
                isProcessing = true
                runOnUiThread { lookupVisitor(scanned) }
            }
        }
    }

    private fun lookupVisitor(data: String) {
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
                AndroidNetworking.get(Constants.getVisitorPassByStaffNoOrName)
                    .addQueryParameter("keyword", data)
                    // ✅ Send flow param — PHP uses this to filter by registration_source
                    .addQueryParameter(
                        "flow",
                        when {
                            StaticData.invitation  -> "invitation"
                            StaticData.collectCard -> "collect_card"
                            else                   -> "walk_in"
                        }
                    )
                    .setTag(Constants.getVisitorPassByStaffNoOrName)
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            Log.d("PORTRAIT_LOOKUP", response.toString())

                            // ✅ Check for no_invitation message from PHP
                            // PHP returns this when invitation flow but no web-created invitation found
                            val message = response.optString("message", "")
                            if (message == "no_invitation") {
                                Common.showToast(
                                    applicationContext,
                                    "No invitation found. Please use Collect Card.",
                                    Common.ToastType.WARNING
                                )
                                finish()
                                startActivity(Intent(this@PortraitCaptureActivity, SelectOptionActivity::class.java))
                                return
                            }

                            val dataArray = response.optJSONArray("data")
                            if (dataArray == null || dataArray.length() == 0) {
                                Common.showToast(applicationContext, "No Records Found", Common.ToastType.ERROR)
                                startActivity(Intent(this@PortraitCaptureActivity, SelectOptionActivity::class.java))
                                return
                            }

                            val obj = dataArray.optJSONObject(0) ?: run {
                                Common.showToast(applicationContext, "No Records Found", Common.ToastType.ERROR)
                                startActivity(Intent(this@PortraitCaptureActivity, SelectOptionActivity::class.java))
                                return
                            }

                            val visitor = GetStaffPassByStaffNoOrNameResponseItem()
                            visitor.name       = obj.optString("fullName", obj.optString("visitorName", obj.optString("name", "")))
                            visitor.icNo       = obj.optString("icNo", obj.optString("icPassport", ""))
                            visitor.passportNo = obj.optString("passportNo", "")
                            visitor.mobileNo   = obj.optString("phoneNo", obj.optString("mobileNo", ""))
                            visitor.email      = obj.optString("email", "")
                            visitor.username   = obj.optString("staffNo", obj.optString("username", ""))
                            visitor.visitorType = obj.optString("visitorType", "")

                            // ✅ Also set invitationId from response for photo upload later
                            val invId = obj.optInt("id", -1)
                            Log.d("PORTRAIT_ID", "invitationId from response: $invId full obj: ${obj.toString()}")
                            if (invId > 0) StaticData.invitationId = invId

                            StaticData.request.ic       = visitor.icNo
                            StaticData.request.passport = visitor.passportNo

                            if (StaticData.invitation) {
                                val isVvipByType = visitor.visitorType.equals("VVIP", ignoreCase = true)
                                if (isVvipByType) {
                                    StaticData.isVvip    = true
                                    StaticData.vvipName  = visitor.name
                                    StaticData.vvipIc    = visitor.icNo.takeIf { it.isNotBlank() } ?: visitor.passportNo
                                    startActivity(Intent(this@PortraitCaptureActivity, VvipConfirmActivity::class.java))
                                    return
                                }
                            }

                            if (!StaticData.collectCard) {
                                val passportNo = visitor.passportNo
                                if (passportNo.isNotBlank()) {
                                    val icNo = visitor.icNo
                                    if (icNo.isBlank()) {
                                        StaticData.isForeigner  = true
                                        StaticData.request.ic   = passportNo
                                        startActivity(Intent(this@PortraitCaptureActivity, CardDetailsActivity::class.java))
                                        return
                                    }
                                }
                                startActivity(Intent(this@PortraitCaptureActivity, NoticeActivity::class.java))
                                return
                            }

                            Common.showToast(applicationContext, "Scan Card now.", Common.ToastType.INFO)
                            val passportNo = visitor.passportNo
                            if (passportNo.isNotBlank() && visitor.icNo.isBlank()) {
                                StaticData.request.ic = passportNo
                            }
                            startActivity(Intent(this@PortraitCaptureActivity, CollectCardActivity::class.java))
                        }

                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Log.e("Portrait", anError.errorDetail ?: "unknown error")
                            Common.showToast(applicationContext, "No Records Found", Common.ToastType.ERROR)
                            finish()
                        }
                    })
            }
        }.start()
    }

    override fun onResume() {
        super.onResume()
        // ✅ Start camera
        barcodeView.resume()
    }

    override fun onPause() {
        super.onPause()
        // ✅ Stop camera
        barcodeView.pause()
    }
}