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
import com.safeg.models.GetStaffPassByStaffNoOrNameResponseItem
import com.safeg.utils.Common
import com.safeg.utils.SslUtils
import org.json.JSONObject

class VvipQrScanActivity : AppCompatActivity() {

    private lateinit var barcodeView: DecoratedBarcodeView
    private var isProcessing = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        // ✅ Use camera QR layout
        setContentView(R.layout.activity_qr_scan_custom)
        val title = intent.getStringExtra("title") ?: "Scan Collect Card QR Code"
        findViewById<android.widget.TextView>(R.id.tvTitle).text = title

        barcodeView = findViewById(R.id.barcode_scanner)
        // ✅ Show camera view
        barcodeView.visibility = View.VISIBLE

        findViewById<ImageView>(R.id.ivBack).setOnClickListener { finish() }

        // ✅ Camera continuous decode
        barcodeView.decodeContinuous { result ->
            val scanned = result?.text?.trim() ?: return@decodeContinuous
            if (scanned.isNotEmpty() && !isProcessing) {
                isProcessing = true
                runOnUiThread { lookupVisitor(scanned) }
            }
        }
    }

    private fun lookupVisitor(qrData: String) {
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
                // ✅ Changed POST → GET with keyword param
                AndroidNetworking.get(Constants.getVisitorPassByStaffNoOrName)
                    .addQueryParameter("keyword", qrData)
                    .setTag("vvip_lookup")
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            Log.d("VVIP_LOOKUP", response.toString())

                            // ✅ Parse new { "status": "success", "data": [...] } format
                            val dataArray = response.optJSONArray("data")
                            if (dataArray == null || dataArray.length() == 0) {
                                Common.showToast(applicationContext, "No Visitor Found for this QR", Common.ToastType.ERROR)
                                isProcessing = false
                                return
                            }

                            val obj = dataArray.optJSONObject(0) ?: run {
                                Common.showToast(applicationContext, "No Visitor Found for this QR", Common.ToastType.ERROR)
                                isProcessing = false
                                return
                            }

                            // ✅ Map new field names
                            val visitor = GetStaffPassByStaffNoOrNameResponseItem()
                            visitor.name = obj.optString("fullName", obj.optString("visitorName", obj.optString("name", "")))
                            visitor.icNo = obj.optString("icNo", obj.optString("icPassport", ""))
                            visitor.passportNo = obj.optString("passportNo", "")
                            visitor.mobileNo = obj.optString("phoneNo", obj.optString("mobileNo", ""))
                            visitor.username = obj.optString("staffNo", obj.optString("username", ""))
                            visitor.visitorType = obj.optString("visitorType", "")
                            val invId = obj.optInt("id", -1)
                            if (invId > 0) StaticData.invitationId = invId
                            Log.d("VVIP_ID", "invitationId set: $invId")

                            Log.d("VVIP_DEBUG", "visitorType=${visitor.visitorType} icNo=${visitor.icNo} name=${visitor.name}")

                            StaticData.request.ic = visitor.icNo
                            StaticData.request.passport = visitor.passportNo

                            // ✅ VVIP
                            if (visitor.visitorType.equals("VVIP", ignoreCase = true)) {
                                StaticData.isVvip = true
                                StaticData.vvipName = visitor.name
                                StaticData.vvipIc = visitor.icNo.takeIf { it.isNotBlank() } ?: visitor.passportNo
                                startActivity(Intent(this@VvipQrScanActivity, VvipConfirmActivity::class.java))
                                return
                            }

                            // ✅ Collect Card
                            if (StaticData.collectCard) {
                                val passportNo = visitor.passportNo
                                if (passportNo.isNotBlank() && visitor.icNo.isBlank()) {
                                    StaticData.isForeigner = true
                                    StaticData.request.passport = passportNo
                                    StaticData.request.ic = passportNo
                                }
                                startActivity(Intent(this@VvipQrScanActivity, CollectCardActivity::class.java))
                                return
                            }

                            // ✅ Foreigner
                            if (visitor.passportNo.isNotBlank() && visitor.icNo.isBlank()) {
                                StaticData.isForeigner = true
                                StaticData.request.ic = visitor.passportNo
                                startActivity(Intent(this@VvipQrScanActivity, CardDetailsActivity::class.java))
                                return
                            }

                            // ✅ Local
                            startActivity(Intent(this@VvipQrScanActivity, NoticeActivity::class.java))
                        }

                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Log.e("VVIP", "Lookup error: ${anError.errorDetail}")
                            Common.showToast(applicationContext, "Network Error — Please Try Again", Common.ToastType.ERROR)
                            isProcessing = false
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