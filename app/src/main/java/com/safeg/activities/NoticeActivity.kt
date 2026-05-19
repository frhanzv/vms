package com.safeg.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import com.safeg.R
import com.safeg.databinding.ActivityNoticeBinding

class NoticeActivity : AppCompatActivity(), View.OnClickListener {

    private lateinit var binding: ActivityNoticeBinding

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityNoticeBinding.inflate(layoutInflater)
        setContentView(binding.root)
        setListeners()
    }

    private fun setListeners() {
        binding.rlOkay.setOnClickListener(this)
        binding.ivBack.setOnClickListener(this)
    }

    override fun onClick(view: View?) {
        when (view?.id) {
            R.id.rlOkay -> {
                finish()
                startActivity(Intent(this, CardDetailsActivity::class.java))
            }
            R.id.ivBack -> {
                finish()
            }
        }
    }
}