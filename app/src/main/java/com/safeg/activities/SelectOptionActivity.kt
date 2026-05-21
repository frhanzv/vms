package com.safeg.activities

import android.content.DialogInterface
import android.content.Intent
import android.net.ConnectivityManager
import android.os.Bundle
import android.view.View
import android.widget.EditText
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import com.androidnetworking.AndroidNetworking
import com.androidnetworking.common.Priority
import com.androidnetworking.error.ANError
import com.androidnetworking.interfaces.ParsedRequestListener
import com.awesomedialog.blennersilva.awesomedialoglibrary.AwesomeInfoDialog
import com.awesomedialog.blennersilva.awesomedialoglibrary.AwesomeProgressDialog
import com.awesomedialog.blennersilva.awesomedialoglibrary.interfaces.Closure
import com.safeg.Constants
import com.safeg.R
import com.safeg.StaticData
import com.safeg.databinding.ActivitySelectOptionBinding
import com.safeg.models.DoVisitorPassReqMobile
import com.safeg.models.GetConfigResponseItem
import com.safeg.utils.Common
import com.safeg.utils.SslUtils
import android.content.pm.PackageManager
import com.androidnetworking.interfaces.JSONObjectRequestListener
import org.json.JSONObject
import android.os.Handler

class SelectOptionActivity : AppCompatActivity(), View.OnClickListener {

    private val PERMISSIONS_REQUEST = 1001
    private lateinit var binding: ActivitySelectOptionBinding
    private val configRefreshHandler = Handler()
    private val configRefreshRunnable = object : Runnable {
        override fun run() {
            invokeConfigApiSilent()
            configRefreshHandler.postDelayed(this, 30000)
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivitySelectOptionBinding.inflate(layoutInflater)
        setContentView(binding.root)
        setListeners()
        requestPermissions(false)
    }

    private fun isNetworkAvailable(): Boolean {
        val cm = getSystemService("connectivity") as ConnectivityManager
        val activeNetworkInfo = cm.activeNetworkInfo
        return activeNetworkInfo != null && activeNetworkInfo.isConnected
    }

    private fun invokeConfigApi() {
        if (!isNetworkAvailable()) {
            Common.showToast(this, "No network connection. Please check your connection.", Common.ToastType.WARNING)
            return
        }
        val pDialog = AwesomeProgressDialog(this).apply {
            setCancelable(false); setTitle("Please wait"); setMessage(""); setColoredCircle(R.color.pherosi); show()
        }
        Thread {
            AndroidNetworking.initialize(applicationContext, SslUtils.trustAllClient())
            runOnUiThread {
                AndroidNetworking.get(Constants.getModuleConfig)
                    .setTag(Constants.getModuleConfig)
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            android.util.Log.d("CONFIG", response.toString())
                            val data = response.optJSONObject("data")
                            if (data != null) {
                                StaticData.moduleConfig.walkIn = data.optBoolean("walk_in", true)
                                StaticData.moduleConfig.invitation = data.optBoolean("invitation", true)
                                StaticData.moduleConfig.collectCard = data.optBoolean("collect_card", true)
                                StaticData.moduleConfig.vvip = data.optBoolean("vvip", true)
                                StaticData.moduleConfig.vpOCR = data.optBoolean("vpOCR", true)
                                StaticData.moduleConfig.vpFacial = data.optBoolean("vpFacial", true)
                                val visitorFieldsObj = data.optJSONObject("visitor_fields")
                                StaticData.visitorFieldsJson = visitorFieldsObj?.toString() ?: "{}"
                                android.util.Log.d("APPLY_FIELDS", "saved visitorFields: ${StaticData.moduleConfig.visitorFields}") // ✅ add
                                android.util.Log.d("APPLY_FIELDS", "moduleConfig hashCode: ${StaticData.moduleConfig.hashCode()}") // ✅ add
                                applyFeatureFlags()
                            } else {
                                Common.showToast(applicationContext, "Config Error: Invalid response", Common.ToastType.ERROR)
                            }
                        }
                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Common.showToast(applicationContext, "Config Error: ${anError.errorCode}", Common.ToastType.ERROR)
                        }
                    })
            }
        }.start()
    }

    private fun invokeConfigApiSilent() {
        if (!isNetworkAvailable()) return
        Thread {
            AndroidNetworking.initialize(applicationContext, SslUtils.trustAllClient())
            runOnUiThread {
                AndroidNetworking.get(Constants.getModuleConfig)
                    .setTag(Constants.getModuleConfig)
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            val data = response.optJSONObject("data") ?: return
                            StaticData.moduleConfig.walkIn = data.optBoolean("walk_in", true)
                            StaticData.moduleConfig.invitation = data.optBoolean("invitation", true)
                            StaticData.moduleConfig.collectCard = data.optBoolean("collect_card", true)
                            StaticData.moduleConfig.vvip = data.optBoolean("vvip", true)
                            StaticData.moduleConfig.vpOCR = data.optBoolean("vpOCR", true)
                            StaticData.moduleConfig.vpFacial = data.optBoolean("vpFacial", true)
                            val visitorFieldsObj = data.optJSONObject("visitor_fields")
                            StaticData.visitorFieldsJson = visitorFieldsObj?.toString() ?: "{}"
                            applyFeatureFlags()
                        }
                        override fun onError(anError: ANError) {}
                    })
            }
        }.start()
    }

    private fun applyFeatureFlags() {
        binding.rlMalaysianPr.visibility = if (StaticData.moduleConfig.invitation) View.VISIBLE else View.GONE
        binding.rlForeigner.visibility = if (StaticData.moduleConfig.walkIn) View.VISIBLE else View.GONE
        binding.rlCollect.visibility = if (StaticData.moduleConfig.collectCard) View.VISIBLE else View.GONE
    }

    private fun setListeners() {
        binding.rlForeigner.setOnClickListener(this)
        binding.rlCollect.setOnClickListener(this)
        binding.rlMalaysianPr.setOnClickListener(this)
        binding.ipSet.setOnClickListener(this)
    }

    private fun showIpSetDialog() {
        val editText = EditText(this)
        editText.hint = "e.g. http://192.168.1.10:8080"
        editText.setText(Constants.base_url)
        val padding = resources.getDimensionPixelSize(com.intuit.sdp.R.dimen._16sdp)
        editText.setPadding(padding, padding, padding, padding)

        AlertDialog.Builder(this)
            .setTitle("Set Server IP / Base URL")
            .setMessage("Enter server base URL")
            .setView(editText)
            .setCancelable(false)
            .setPositiveButton("Save") { dialog, _ ->
                val url = editText.text.toString().trim()
                if (url.isEmpty()) {
                    Common.showToast(this, "Base URL cannot be empty", Common.ToastType.WARNING)
                    return@setPositiveButton
                }
                Constants.base_url = url
                Constants.refreshUrls()
                Common.showToast(this, "Base URL saved: $url", Common.ToastType.SUCCESS)
                invokeConfigApi()
                dialog.dismiss()
            }
            .setNegativeButton("Cancel") { dialog, _ -> dialog.dismiss() }
            .show()
    }

    override fun onClick(view: View?) {
        when (view?.id) {
            R.id.rlMalaysianPr -> {
                StaticData.isForeigner = false
                StaticData.collectCard = false
                StaticData.invitation = true
                StaticData.isVvip = false
                StaticData.base64_face = ""
                if (StaticData.moduleConfig.vpOCR) {
                    startActivity(Intent(this, PortraitCaptureActivity::class.java))
                } else {
                    startActivity(Intent(this, WelcomeActivity::class.java))
                }
            }
            R.id.rlForeigner -> {
                StaticData.isForeigner = true
                StaticData.invitation = false
                StaticData.collectCard = false
                StaticData.isVvip = false
                StaticData.base64_face = ""
                startActivity(Intent(this, WelcomeActivity::class.java))
            }
            R.id.rlCollect -> {
                StaticData.collectCard = true
                StaticData.invitation = false
                StaticData.isForeigner = false
                StaticData.isVvip = false
                StaticData.base64_face = ""
                startActivity(Intent(this, VvipQrScanActivity::class.java))
            }
            R.id.ipSet -> {
                showIpSetDialog()
            }
        }
    }

    private fun requestPermissions(showToast: Boolean) {
        if (checkSelfPermission("android.permission.CAMERA") == PackageManager.PERMISSION_GRANTED) {
            invokeConfigApi()
            return
        }
        if (showToast) {
            Common.showToast(this, "Please allow permissions to continue.", Common.ToastType.WARNING)
        }
        requestPermissions(arrayOf("android.permission.CAMERA"), PERMISSIONS_REQUEST)
    }

    override fun onRequestPermissionsResult(requestCode: Int, permissions: Array<String>, grantResults: IntArray) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        requestPermissions(true)
    }

    override fun onBackPressed() {
        val dialog = AwesomeInfoDialog(this)
        dialog.setTitle("Confirmation")
        dialog.setMessage("Do you really want to close the ${getString(R.string.app_name)}?")
        dialog.setColoredCircle(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.dialogInfoBackgroundColor)
        dialog.setDialogIconAndColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.drawable.ic_dialog_info, com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.white)
        dialog.setCancelable(false)
        dialog.setPositiveButtonText("Yes")
        dialog.setPositiveButtonbackgroundColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.dialogInfoBackgroundColor)
        dialog.setPositiveButtonTextColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.white)
        dialog.setPositiveButtonClick(object : Closure {
            override fun exec() { dialog.hide(); finish() }
        })
        dialog.setNegativeButtonText("No")
        dialog.setNegativeButtonbackgroundColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.dialogInfoBackgroundColor)
        dialog.setNegativeButtonTextColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.white)
        dialog.setNegativeButtonClick(object : Closure {
            override fun exec() { dialog.hide() }
        })
        dialog.show()
    }

    override fun onStart() {
        super.onStart()
        StaticData.request = DoVisitorPassReqMobile()
        applyFeatureFlags()
    }
    override fun onResume() {
        super.onResume()
        invokeConfigApi()
        configRefreshHandler.postDelayed(configRefreshRunnable, 30000)
    }
    override fun onPause() {
        super.onPause()
        configRefreshHandler.removeCallbacks(configRefreshRunnable)
    }
}