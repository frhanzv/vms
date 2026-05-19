package com.safeg.activities

import android.content.Intent
import android.os.Bundle
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import com.awesomedialog.blennersilva.awesomedialoglibrary.AwesomeInfoDialog
import com.awesomedialog.blennersilva.awesomedialoglibrary.interfaces.Closure
import com.safeg.R
import com.safeg.StaticData
import com.safeg.models.DoVisitorPassReqMobile

class VvipThankYouActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_vvip_thankyou)

        val tvVvipName = findViewById<TextView>(R.id.tvVvipName)
        val name = StaticData.vvipName
        tvVvipName.text = if (name.isBlank()) "" else name

        showSuccessDialog()
    }

    private fun showSuccessDialog() {
        val dialog = AwesomeInfoDialog(this)
        dialog.setTitle("VVIP Card Issued!")
        val name = StaticData.vvipName
        dialog.setMessage("VVIP card has been successfully issued to ${if (name.isBlank()) "visitor" else name}. Welcome!")
        dialog.setColoredCircle(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.dialogInfoBackgroundColor)
        dialog.setDialogIconAndColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.drawable.ic_dialog_info, com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.white)
        dialog.setCancelable(false)
        dialog.setPositiveButtonText("OK")
        dialog.setPositiveButtonbackgroundColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.dialogInfoBackgroundColor)
        dialog.setPositiveButtonTextColor(com.awesomedialog.blennersilva.awesomedialoglibrary.R.color.white)
        dialog.setPositiveButtonClick(object : Closure {
            override fun exec() {
                dialog.hide()
                goHome()
            }
        })
        dialog.show()
    }

    private fun goHome() {
        StaticData.isVvip = false
        StaticData.vvipName = ""
        StaticData.vvipIc = ""
        StaticData.base64_face = ""
        StaticData.request = DoVisitorPassReqMobile()
        val intent = Intent(this, SelectOptionActivity::class.java)
        intent.flags = 0x10080000
        startActivity(intent)
    }
}