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

class SelectOptionActivity : AppCompatActivity(), View.OnClickListener {

    private val PERMISSIONS_REQUEST = 1001
    private lateinit var binding: ActivitySelectOptionBinding

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
                    .getAsObject(GetConfigResponseItem::class.java, object : ParsedRequestListener<GetConfigResponseItem> {
                        override fun onResponse(response: GetConfigResponseItem) {
                            pDialog.hide()
                            StaticData.moduleConfig = response
                        }
                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Common.showToast(applicationContext, "Config Error: ${anError.errorCode} — ${anError.errorDetail}", Common.ToastType.ERROR)
                        }
                    })
            }
        }.start()
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
        if (checkSelfPermission("android.permission.CAMERA") == 0) {
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
    }
}