package com.safeg.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.ImageView
import android.widget.RelativeLayout
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import com.safeg.R
import com.safeg.StaticData

class VvipConfirmActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_vvip_confirm)

        val tvVvipName = findViewById<TextView>(R.id.tvVvipName)
        val name = StaticData.vvipName
        tvVvipName.text = if (name.isBlank()) "—" else name

        val tvVvipIc = findViewById<TextView>(R.id.tvVvipIc)
        val ic = StaticData.vvipIc
        tvVvipIc.text = if (ic.isBlank()) "—" else ic

        findViewById<ImageView>(R.id.ivBack).setOnClickListener { finish() }

        findViewById<RelativeLayout>(R.id.rlProceed).setOnClickListener {
            startActivity(Intent(this, FaceDetectionActivity::class.java))
        }
    }
}