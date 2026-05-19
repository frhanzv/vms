package com.safeg.activities

import android.app.Activity
import android.content.Intent
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.util.Log
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import com.androidnetworking.AndroidNetworking
import com.androidnetworking.common.Priority
import com.androidnetworking.error.ANError
import com.androidnetworking.interfaces.JSONObjectRequestListener
import com.awesomedialog.blennersilva.awesomedialoglibrary.AwesomeInfoDialog
import com.awesomedialog.blennersilva.awesomedialoglibrary.AwesomeProgressDialog
import com.awesomedialog.blennersilva.awesomedialoglibrary.interfaces.Closure
import com.safeg.Constants
import com.safeg.R
import com.safeg.StaticData
import com.safeg.models.DoVisitorPassReqMobile
import com.safeg.utils.Common
import com.safeg.utils.SslUtils
import org.json.JSONObject
import java.util.concurrent.TimeUnit

class ThankYouActivity : AppCompatActivity() {

    companion object {
        const val EXTRA_FLOW = "extra_flow"
        const val FLOW_COLLECT_CARD = "collect_card"
        const val FLOW_INVITATION = "invitation"
        const val FLOW_WALKIN = "walkin"
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_thank_you)

        var flow = intent.getStringExtra(EXTRA_FLOW)
        if (flow == null) flow = FLOW_WALKIN

        val tvMessage = findViewById<TextView>(R.id.tvThankYouMessage)

        if (flow == FLOW_INVITATION || flow == FLOW_COLLECT_CARD) {
            tvMessage?.setText("Thank You, Have a Great Day!\nYour visitor card has been issued successfully.")
            autoRedirectToMain(3000L)
        } else {
            tvMessage?.setText("An email/SMS has been sent\nto your phone as digital\nconfirmation for your visit")
            invokeRegistrationApi()
        }
    }

    private fun autoRedirectToMain(delayMs: Long) {
        Handler(Looper.getMainLooper()).postDelayed({
            resetStaticData()
            finish()
            val intent = Intent(this, SelectOptionActivity::class.java)
            // ✅ proper flag constants
            intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
            startActivity(intent)
        }, delayMs)
    }

    private fun resetStaticData() {
        StaticData.request = DoVisitorPassReqMobile()
        StaticData.base64_mykad = ""
        StaticData.base64_face = ""
        StaticData.base64_mysejahtera = ""
        StaticData.base64_vaccinationCertificate = ""
        StaticData.isForeigner = false
        StaticData.collectCard = false
        StaticData.invitation = false
        StaticData.isVvip = false
        StaticData.vvipName = ""
        StaticData.vvipIc = ""
        StaticData.invitationId = -1  // ✅ reset invitationId
    }

    private fun invokeRegistrationApi() {
        val r = StaticData.request

        // ✅ Build new API payload — map old field names to new VMS field names
        val payload = JSONObject().apply {
            // ✅ Required fields
            put("visitorName", r.fullName)
            put("phoneNo", r.contactNo)
            put("visitReason", r.reason)
            if (r.country > 0)  put("country", r.country)

            // ✅ IC — local uses ic, foreigner uses passport
            if (r.passport.isNotBlank()) put("icNo", r.passport)
            else if (r.ic.isNotBlank()) put("icNo", r.ic)

            if (r.email.isNotBlank())    put("email", r.email)
            if (r.cpnName.isNotBlank())  put("companyName", r.cpnName)
            if (r.regNum.isNotBlank())   put("vehicleNo", r.regNum)
            if (r.staffNo.isNotBlank())  put("invitedBy", r.staffNo)
            if (r.sex.isNotBlank())      put("sex", r.sex)
            if (r.resident.isNotBlank()) put("resident", r.resident)
            if (r.visitorTypeId > 0)     put("visitorTypeId", r.visitorTypeId)

            // ✅ Address — concatenate add1/add2/add3
            val addressParts = listOf(r.add1, r.add2, r.add3).filter { it.isNotBlank() }
            if (addressParts.isNotEmpty()) put("address", addressParts.joinToString(", "))

            if (r.postalCode.isNotBlank() && r.postalCode != "-1") put("postcode", r.postalCode)
            if (r.birthday.isNotBlank())  put("dateOfBirth", r.birthday)

            // ✅ Photo
            val face = StaticData.base64_face
            if (face.isNotBlank()) put("photo_base64", "data:image/png;base64,$face")
        }

        Log.d("PAYLOAD", payload.toString(2))
        Log.d("PAYLOAD_URL", Constants.visitorPassRegistrationLite)

        val pDialog = AwesomeProgressDialog(this).apply {
            setCancelable(false); setTitle("Please wait"); setMessage(""); setColoredCircle(R.color.pherosi); show()
        }

        Thread {
            val client = SslUtils.trustAllClient().newBuilder()
                .connectTimeout(180L, TimeUnit.SECONDS)
                .readTimeout(180L, TimeUnit.SECONDS)
                .writeTimeout(180L, TimeUnit.SECONDS)
                .build()
            AndroidNetworking.initialize(applicationContext, client)
            runOnUiThread {
                AndroidNetworking.post(Constants.visitorPassRegistrationLite)
                    .setTag(Constants.visitorPassRegistrationLite)
                    .setPriority(Priority.HIGH)
                    .addJSONObjectBody(payload)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            Log.d("PAYLOAD_RESPONSE", response.toString())
                            try {
                                val status = response.optString("status", "")

                                // ✅ Parse invitationId from data.id
                                val dataObj = response.optJSONObject("data")
                                val id = dataObj?.optInt("id", -1) ?: -1
                                if (id > 0) {
                                    StaticData.invitationId = id
                                    Log.d("INVITATION_ID", "Saved invitationId=$id")
                                }

                                when {
                                    // ✅ New VMS response
                                    status.equals("success", ignoreCase = true) -> {
                                        showInfoDialog(this@ThankYouActivity, "Process completed successfully.", "Information")
                                    }
                                    // ✅ Old backend backward compat
                                    status.equals("OK", ignoreCase = true) -> {
                                        showInfoDialog(this@ThankYouActivity, "Process completed successfully.", "Information")
                                    }
                                    else -> {
                                        val msg = response.optString("message", "").ifBlank {
                                            response.optJSONObject("error")
                                                ?.optString("error", "Registration failed")
                                                ?: "Registration failed"
                                        }
                                        showErrorDialog(this@ThankYouActivity, msg, status)
                                    }
                                }
                            } catch (e: Exception) {
                                Log.e("PAYLOAD_PARSE", "Parse error: ${e.message}")
                                showErrorDialog(this@ThankYouActivity, "Unexpected response from server.", "Error")
                            }
                        }

                        override fun onError(anError: ANError?) {
                            pDialog.hide()
                            Log.e("PAYLOAD_ERROR", "errorBody: ${anError?.errorBody}, errorDetail: ${anError?.errorDetail}")
                            showErrorDialog(this@ThankYouActivity, anError?.errorBody, "API Response")
                            Common.showToast(applicationContext, "Error Code : ${anError?.errorCode}, Details : ${anError?.errorDetail}", Common.ToastType.ERROR)
                        }
                    })
            }
        }.start()
    }

    fun showInfoDialog(context: Activity, msg: String?, title: String?) {
        val dialog = AwesomeInfoDialog(context)
        dialog.setTitle(title)
        dialog.setMessage(msg)
        dialog.setColoredCircle(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.dialogInfoBackgroundColor)
        dialog.setDialogIconAndColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.drawable.ic_dialog_info, com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.white)
        dialog.setCancelable(false)
        dialog.setPositiveButtonText("OK")
        dialog.setPositiveButtonbackgroundColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.dialogInfoBackgroundColor)
        dialog.setPositiveButtonTextColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.white)
        dialog.setPositiveButtonClick(object : Closure {
            override fun exec() {
                dialog.hide()
                resetStaticData()
                finish()
                val intent = Intent(this@ThankYouActivity, SelectOptionActivity::class.java)
                // ✅ proper flag constants
                intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
                startActivity(intent)
            }
        })
        dialog.show()
    }

    fun showErrorDialog(context: Activity, msg: String?, title: String?) {
        val dialog = AwesomeInfoDialog(context)
        dialog.setTitle(title)
        dialog.setMessage(msg)
        dialog.setColoredCircle(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.dialogErrorBackgroundColor)
        dialog.setDialogIconAndColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.drawable.ic_dialog_error, com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.white)
        dialog.setCancelable(false)
        dialog.setPositiveButtonText("OK")
        dialog.setPositiveButtonbackgroundColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.dialogErrorBackgroundColor)
        dialog.setPositiveButtonTextColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.white)
        dialog.setPositiveButtonClick(object : Closure {
            override fun exec() {
                dialog.hide()
                resetStaticData()
                finish()
                startActivity(Intent(this@ThankYouActivity, WelcomeActivity::class.java))
            }
        })
        dialog.show()
    }
}