package com.safeg.activities

import android.app.DatePickerDialog
import android.content.ContentResolver
import android.content.Intent
import android.graphics.Bitmap
import android.graphics.BitmapFactory
import android.graphics.RenderEffect
import android.graphics.Shader
import android.graphics.Typeface
import android.hardware.usb.UsbManager
import android.os.Build
import android.os.Bundle
import android.os.Handler
import android.os.ParcelFileDescriptor
import android.provider.MediaStore
import android.text.Editable
import android.text.TextWatcher
import android.util.Log
import android.view.View
import android.widget.AdapterView
import android.widget.DatePicker
import android.widget.TextView
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.res.ResourcesCompat
import com.androidnetworking.AndroidNetworking
import com.androidnetworking.common.Priority
import com.androidnetworking.error.ANError
import com.androidnetworking.interfaces.JSONObjectRequestListener
import com.awesomedialog.blennersilva.awesomedialoglibrary.AwesomeProgressDialog
import com.intellego.morphosmart.driver.DeviceException
import com.intellego.morphosmart.driver.DeviceProbe
import com.intellego.morphosmart.driver.MorphoSmart
import com.intellego.mykad.CardHolderInfo
import com.safeg.Constants
import com.safeg.R
import com.safeg.StaticData
import com.safeg.cardreader.*
import com.safeg.databinding.ActivityCardDetailsBinding
import com.safeg.models.*
import com.safeg.utils.Common
import com.safeg.utils.SslUtils
import com.safeg.utils.Utils
import com.google.gson.Gson
import org.json.JSONObject
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*

class CardDetailsActivity : AppCompatActivity(),
    OnReadCardTaskCompleteListener,
    OnVerifyFpTaskCompleteListener,
    OnReadCardVerifyFpTaskCompleteListener {

    private lateinit var binding: ActivityCardDetailsBinding
    private lateinit var montserrat: Typeface

    lateinit var cityModelList: ArrayList<GetCityByStateResponseItem>
    lateinit var cityStringList: ArrayList<String>
    lateinit var countryModelList: ArrayList<GetActiveCountryResponseItem>
    lateinit var countryStringList: ArrayList<String>
    private var deviceProbe: DeviceProbe? = null
    lateinit var genderStringList: ArrayList<String>
    lateinit var mUtils: Utils
    private var morphoSmart: MorphoSmart? = null
    lateinit var plksExpiryStringList: ArrayList<String>
    private var readCardAsyncManager: ReadCardAsyncTaskManager? = null
    private var readCardVerifyFpAsyncTaskManager: ReadCardVerifyFpAsyncTaskManager? = null
    lateinit var reasonsStringList: ArrayList<String>
    lateinit var residentStringList: ArrayList<String>
    private var runnable: Runnable? = null
    var selectedCityName: String? = null
    lateinit var stateModelList: ArrayList<GetStateByCountryResponseItem>
    lateinit var stateStringList: ArrayList<String>
    lateinit var vehicleTypeStringList: ArrayList<String>
    private var verifyFpAsyncManager: VerifyFpAsyncTaskManager? = null
    lateinit var vinTypeModelList: ArrayList<GetActiveVehicleTypeResponseItem>

    var selectedCountry: Int = -1
    var selectedvehicleType: Int = -1
    private val handler = Handler()
    var selectedPlksExpiry: String = ""
    var selectedResident: String = ""
    var selectedGender: String = ""
    var selectedVehicleCategory: String = ""
    var selectedVehicleType: String = ""

    companion object {
        private const val REQUEST_CAMERA = 10002
        private const val REQUEST_GALLERY = 10001
    }

    private fun safeValue(value: String?): String {
        return if (value == null || value == "null") "" else value
    }

    private fun applyFontToSpinner(spinner: View) {
        try {
            var clazz: Class<*>? = spinner.javaClass
            while (clazz != null) {
                for (field in clazz.declaredFields) {
                    field.isAccessible = true
                    try {
                        val value = field.get(spinner)
                        if (value is TextView) {
                            value.typeface = montserrat
                            value.textSize = 13f
                        }
                    } catch (ignored: Exception) {}
                }
                clazz = clazz.superclass
            }
        } catch (e: Exception) {
            Log.w("Font", "applyFontToSpinner: ${e.message}")
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        startTimer()

        binding = ActivityCardDetailsBinding.inflate(layoutInflater)
        setContentView(binding.root)

        montserrat = ResourcesCompat.getFont(this, R.font.montserrat_bold) ?: Typeface.DEFAULT_BOLD

        mUtils = Utils()

        binding.llDateOfBirth.setOnClickListener { dateOfBirthClicked() }
        binding.tvDateOfBirth.setOnClickListener { dateOfBirthClicked() }
        binding.ivDateOfBirth.setOnClickListener { dateOfBirthClicked() }
        binding.tvDateOfBirthTitle.setOnClickListener { dateOfBirthClicked() }
        binding.llPlksExpiry.setOnClickListener { plksExpiryClicked() }
        binding.tvPlksExpiry.setOnClickListener { plksExpiryClicked() }
        binding.ivPlksExpiry.setOnClickListener { plksExpiryClicked() }
        binding.tvPlksExpiryTitle.setOnClickListener { plksExpiryClicked() }

        val base64mykad = StaticData.base64_mykad
        if (!(base64mykad == null || base64mykad.isEmpty())) {
            binding.ivCard.setImageBitmap(mUtils.base64ToBitmap(base64mykad))
        }

        val fullNamePre = StaticData.request.fullName
        if (!(fullNamePre == null || fullNamePre.isEmpty())) {
            binding.etCardHolderName.setText(StaticData.request.fullName)
        }

        val add1Pre = StaticData.request.add1
        if (!(add1Pre == null || add1Pre.isEmpty())) {
            binding.etAddress.setText("${binding.etAddress.text}${StaticData.request.add1}")
        }

        val add2Pre = StaticData.request.add2
        if (!(add2Pre == null || add2Pre.isEmpty())) {
            binding.etAddress.setText("${binding.etAddress.text}\n${StaticData.request.add2}")
        }

        val add3Pre = StaticData.request.add3
        if (!(add3Pre == null || add3Pre.isEmpty())) {
            binding.etAddress.setText("${binding.etAddress.text}\n${StaticData.request.add3}")
        }

        if (StaticData.isForeigner) {
            binding.tvTitle.setText("Passport Details")
            binding.tvIcNumber.setText("Passport Number")
            binding.llResident.visibility = View.GONE
            binding.viewResident.visibility = View.GONE
            binding.llCountry.visibility = View.VISIBLE
            binding.llDateOfBirth.visibility = View.GONE
            binding.llPlksExpiry.visibility = View.GONE
            binding.llState.visibility = View.GONE
            binding.llCity.visibility = View.GONE
            binding.llPostalCode.visibility = View.GONE
            binding.cvCard.visibility = View.GONE
            binding.tvScanKad.visibility = View.GONE
            binding.dottedBox.visibility = View.GONE
            binding.viewVinType.visibility = View.GONE
            binding.viewVinCat.visibility = View.GONE
            binding.viewGender.visibility = View.GONE
            binding.llVinType.visibility = View.GONE
            binding.viewAddress.visibility = View.GONE
            binding.llAddress.visibility = View.GONE
            binding.llVinCat.visibility = View.GONE
            binding.llGender.visibility = View.GONE
        } else {
            binding.tvTitle.setText("MyKad Details")
            binding.tvIcNumber.setText("IC Number")
            binding.llPlksExpiry.visibility = View.GONE
            binding.llResident.visibility = View.GONE
            binding.viewResident.visibility = View.GONE
            binding.llCountry.visibility = View.GONE
            binding.dottedBox.visibility = View.VISIBLE
            binding.viewVinType.visibility = View.GONE
            binding.cvCard.visibility = View.VISIBLE
            binding.viewVinCat.visibility = View.GONE
            binding.viewGender.visibility = View.GONE
            binding.llVinType.visibility = View.GONE
            binding.llVinCat.visibility = View.GONE
            binding.llGender.visibility = View.GONE
            binding.llIc.visibility = View.VISIBLE
            binding.viewAddress.visibility = View.VISIBLE
            binding.llAddress.visibility = View.VISIBLE
            binding.llDateOfBirth.visibility = View.GONE
            binding.llState.visibility = View.GONE
            binding.llCity.visibility = View.GONE
            binding.llPostalCode.visibility = View.GONE
        }

        invokeGetVehicleTypeApi()
        invokeGetActiveCountryApi()
        initResidentDropdown()
        initGenderDropdown()
        initVehicleCategories()

        binding.ivBack.setOnClickListener { finish() }

        binding.tvCaptrure.setOnClickListener {
            val cameraIntent = Intent(android.provider.MediaStore.ACTION_IMAGE_CAPTURE)
            startActivityForResult(cameraIntent, REQUEST_CAMERA)
        }

        binding.tvUploadPhoto.setOnClickListener {
            val intent = Intent(Intent.ACTION_PICK, MediaStore.Images.Media.EXTERNAL_CONTENT_URI)
            intent.type = "image/*"
            startActivityForResult(Intent.createChooser(intent, "Select Picture"), REQUEST_GALLERY)
        }

        binding.rlNext.setOnClickListener {
            if (binding.etCardHolderName.text.toString().isEmpty()) {
                Common.showToast(this, "Please Enter Card Holder Name", Common.ToastType.WARNING)
                return@setOnClickListener
            }
            if (!StaticData.isForeigner && binding.etCardNumber.text.toString().isEmpty()) {
                Common.showToast(this, "Please Enter ${binding.tvIcNumber.text}", Common.ToastType.WARNING)
                return@setOnClickListener
            }
            if (!StaticData.isForeigner && binding.etAddress.text.toString().isEmpty()) {
                Common.showToast(this, "Please Enter Address", Common.ToastType.WARNING)
                return@setOnClickListener
            }
            if (binding.etContactNumber.text.toString().isEmpty()) {
                Common.showToast(this, "Please Enter Contact Number", Common.ToastType.WARNING)
                return@setOnClickListener
            }
            if (binding.etCompanyName.text.toString().isEmpty()) {
                Common.showToast(this, "Please Enter Company Name", Common.ToastType.WARNING)
                return@setOnClickListener
            }
            if (StaticData.isForeigner && binding.etCardNumber.text.toString().isEmpty()) {
                Common.showToast(this, "Please Enter Passport Number", Common.ToastType.WARNING)
                return@setOnClickListener
            }
            if (selectedCountry == -1) {
                Common.showToast(this, "Please Select Country", Common.ToastType.WARNING)
                return@setOnClickListener
            }
            if (StaticData.isForeigner && binding.etRegNo.text.toString().isEmpty()) {
                Common.showToast(this, "Please Enter Vehicle Registration No", Common.ToastType.WARNING)
                return@setOnClickListener
            }

            val lines = mUtils.getLines(binding.etAddress.text.toString())
            if (lines.size > 0) {
                when (lines.size) {
                    3 -> { StaticData.request.add1 = lines[0]; StaticData.request.add2 = lines[1]; StaticData.request.add3 = lines[2] }
                    2 -> { StaticData.request.add1 = lines[0]; StaticData.request.add2 = lines[1]; StaticData.request.add3 = "" }
                    else -> { StaticData.request.add1 = lines[0]; StaticData.request.add2 = ""; StaticData.request.add3 = "" }
                }
            } else {
                StaticData.request.add1 = ""
                StaticData.request.add2 = ""
                StaticData.request.add3 = ""
            }

            StaticData.request.birthday = ""
            StaticData.request.city = "-1"
            StaticData.request.state = "-1"
            StaticData.request.postalCode = ""
            StaticData.request.clientVisitor = "true"
            StaticData.request.contactNo = binding.etContactNumber.text.toString()
            StaticData.request.country = selectedCountry
            StaticData.request.vinType = selectedvehicleType
            StaticData.request.vinCat = selectedVehicleCategory
            StaticData.request.regNum = binding.etRegNo.text.toString()
            StaticData.request.cpnName = binding.etCompanyName.text.toString()
            StaticData.request.cpnRegID = "123456789012"
            StaticData.request.email = binding.etEmail.text.toString()
            StaticData.request.fullName = binding.etCardHolderName.text.toString()

            if (StaticData.isForeigner) {
                StaticData.request.passport = binding.etCardNumber.text.toString()
                StaticData.request.passportIssueCountry = selectedCountry
                StaticData.request.resident = "Foreigner"
            } else {
                StaticData.request.ic = binding.etCardNumber.text.toString()
                StaticData.request.passport = ""
                StaticData.request.passportIssueCountry = 1
                StaticData.request.resident = "Local"
            }

            StaticData.request.maxHoursForVisitorPassPerIcOnThisMonth = 999
            StaticData.request.plksExpiry = binding.tvPlksExpiry.text.toString()
            StaticData.request.sex = selectedGender

            startActivity(Intent(applicationContext, FaceDetectionActivity::class.java))
        }

        try {
            deviceProbe = DeviceProbe(baseContext)
        } catch (e: DeviceException) {
            e.printStackTrace()
        }

        readCardAsyncManager = ReadCardAsyncTaskManager(this, this)
        readCardAsyncManager!!.handleRetainedTask(lastNonConfigurationInstance)

        verifyFpAsyncManager = VerifyFpAsyncTaskManager(this, this)
        verifyFpAsyncManager!!.handleRetainedTask(lastNonConfigurationInstance)

        readCardVerifyFpAsyncTaskManager = ReadCardVerifyFpAsyncTaskManager(this, this)
        readCardVerifyFpAsyncTaskManager!!.handleRetainedTask(lastNonConfigurationInstance)

        binding.tvScanKad.setOnClickListener { onReadMyKad() }

        if (Build.VERSION.SDK_INT >= 31) {
            binding.cvCard.setRenderEffect(RenderEffect.createBlurEffect(20f, 20f, Shader.TileMode.CLAMP))
        }

        var debounceRunnable: Runnable? = null
        val debounceHandler = Handler()

        binding.etCardNumber.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {}
            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {}
            override fun afterTextChanged(s: Editable?) {
                debounceRunnable?.let { debounceHandler.removeCallbacks(it) }
                debounceRunnable = Runnable {
                    val number = s?.toString()?.trim() ?: return@Runnable
                    if (number.isNotEmpty()) {
                        if (!StaticData.isForeigner && number.length >= 10) {
                            invokeCheckIcExist(number)
                        } else if (StaticData.isForeigner && number.length >= 5) {
                            invokeCheckPassportExist(number)
                        }
                    }
                }
                debounceHandler.postDelayed(debounceRunnable!!, 500)
            }
        })

        if (StaticData.isForeigner) {
            val passportNo = StaticData.request.passport.takeIf { it.isNotBlank() }
                ?: StaticData.request.ic.takeIf { it.isNotBlank() }
                ?: ""
            binding.etCardNumber.setText(passportNo)
            if (passportNo.isNotBlank()) {
                invokeCheckPassportExist(passportNo)
            }
        } else {
            val icNo = StaticData.request.ic.takeIf { it.isNotBlank() } ?: ""
            binding.etCardNumber.setText(icNo)
            if (icNo.isNotBlank()) {
                invokeCheckIcExist(icNo)
            }
            try {
                val lastDigit = Common.parseInt(icNo.substring(icNo.length - 1, icNo.length))
                if (lastDigit % 2 == 1) {
                    binding.spGender.setSelection(0)
                } else {
                    binding.spGender.setSelection(1)
                }
            } catch (th: Throwable) {}
        }
    }

    private fun startTimer() {
        runnable = Runnable { finish() }
        handler.postDelayed(runnable!!, 240000L)
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == REQUEST_CAMERA) {
            if (data != null) {
                try {
                    val extras = data.extras
                    val bitmap = extras?.get("data") as? Bitmap
                    bitmap!!
                    StaticData.base64_mykad = Utils().bitmapToBase64(bitmap)
                    binding.ivCard.setImageBitmap(bitmap)
                } catch (th: Throwable) {
                    return
                }
            }
        }
        if (requestCode == REQUEST_GALLERY) {
            val path = data?.data
            val contentResolver: ContentResolver = contentResolver
            path!!
            val parcelFileDescriptor: ParcelFileDescriptor = contentResolver.openFileDescriptor(path, "r")!!
            val fileDescriptor = parcelFileDescriptor.fileDescriptor
            val bitmap2 = BitmapFactory.decodeFileDescriptor(fileDescriptor)
            parcelFileDescriptor.close()
            StaticData.base64_mykad = Utils().bitmapToBase64(bitmap2!!)
            binding.ivCard.setImageBitmap(bitmap2)
        }
    }

    private fun dateOfBirthClicked() {
        val c = Calendar.getInstance()
        var month = c.get(Calendar.MONTH)
        var day = c.get(Calendar.DAY_OF_MONTH)
        var year = c.get(Calendar.YEAR)
        if (binding.tvDateOfBirth.text.toString().isNotEmpty()) {
            val format = SimpleDateFormat(Constants.date_format)
            try {
                val date = format.parse(binding.tvDateOfBirth.text.toString())
                c.timeInMillis = date!!.time
                month = c.get(Calendar.MONTH)
                day = c.get(Calendar.DAY_OF_MONTH)
                year = c.get(Calendar.YEAR)
            } catch (e: ParseException) { e.printStackTrace() }
        }
        DatePickerDialog(this, { _: DatePicker, y: Int, m: Int, d: Int ->
            c.set(y, m, d)
            val df = SimpleDateFormat(Constants.date_format, Locale.US)
            binding.tvDateOfBirth.text = df.format(Date(c.timeInMillis))
        }, year, month, day).show()
    }

    private fun plksExpiryClicked() {
        val c = Calendar.getInstance()
        var month = c.get(Calendar.MONTH)
        var day = c.get(Calendar.DAY_OF_MONTH)
        var year = c.get(Calendar.YEAR)
        if (binding.tvPlksExpiry.text.toString().isNotEmpty()) {
            val format = SimpleDateFormat(Constants.date_format)
            try {
                val date = format.parse(binding.tvPlksExpiry.text.toString())
                c.timeInMillis = date!!.time
                month = c.get(Calendar.MONTH)
                day = c.get(Calendar.DAY_OF_MONTH)
                year = c.get(Calendar.YEAR)
            } catch (e: ParseException) { e.printStackTrace() }
        }
        DatePickerDialog(this, { _: DatePicker, y: Int, m: Int, d: Int ->
            c.set(y, m, d)
            val df = SimpleDateFormat(Constants.date_format, Locale.US)
            binding.tvPlksExpiry.text = df.format(Date(c.timeInMillis))
        }, year, month, day).show()
    }

    private fun initResidentDropdown() {
        residentStringList = ArrayList()
        residentStringList.add("Local")
        residentStringList.add("Foreigner")
        binding.spResident.setItem(residentStringList)
        binding.spResident.setOnItemSelectedListener(object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View, position: Int, id: Long) {
                selectedResident = residentStringList[position]
            }
            override fun onNothingSelected(parent: AdapterView<*>?) {}
        })
    }

    private fun initGenderDropdown() {
        genderStringList = ArrayList()
        genderStringList.add("Male")
        genderStringList.add("Female")
        binding.spGender.setItem(genderStringList)
        binding.spGender.setOnItemSelectedListener(object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View, position: Int, id: Long) {
                selectedGender = genderStringList[position]
            }
            override fun onNothingSelected(parent: AdapterView<*>?) {}
        })
    }

    private fun initVehicleCategories() {
        genderStringList = ArrayList()
        genderStringList.add("public")
        genderStringList.add("cargo")
        genderStringList.add("non-cargo")
        binding.spVinCat.setItem(genderStringList)
        binding.spVinCat.setOnItemSelectedListener(object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View, position: Int, id: Long) {
                selectedVehicleCategory = genderStringList[position]
            }
            override fun onNothingSelected(parent: AdapterView<*>?) {}
        })
    }

    // ✅ Changed getAsObjectList → getAsJSONObject, parse wrapped { "status", "data": [] }
    private fun invokeGetVehicleTypeApi() {
        val pDialog = AwesomeProgressDialog(this).apply {
            setCancelable(false); setTitle("Please wait"); setMessage(""); setColoredCircle(R.color.pherosi); show()
        }
        Thread {
            AndroidNetworking.initialize(applicationContext, SslUtils.trustAllClient())
            runOnUiThread {
                AndroidNetworking.get(Constants.getActiveVehicleType)
                    .setTag(Constants.getActiveVehicleType)
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            Log.d("VEHICLE_TYPES", response.toString())
                            vinTypeModelList = ArrayList()
                            vehicleTypeStringList = ArrayList()

                            // ✅ Controller always returns empty [] for vehicle types
                            // Parse anyway for future compatibility
                            val dataArray = response.optJSONArray("data")
                            if (dataArray != null && dataArray.length() > 0) {
                                for (i in 0 until dataArray.length()) {
                                    val obj = dataArray.optJSONObject(i) ?: continue
                                    val item = GetActiveVehicleTypeResponseItem()
                                    item.id = obj.optInt("id", -1)
                                    item.name = obj.optString("name", "")
                                    if (item.name.isNotEmpty()) {
                                        vinTypeModelList.add(item)
                                        vehicleTypeStringList.add(item.name)
                                    }
                                }
                            }

                            // ✅ Vehicle types empty from VMS — just hide spinner silently
                            if (vinTypeModelList.isEmpty()) {
                                Log.d("VEHICLE_TYPES", "Empty — VMS does not manage vehicle types")
                                return
                            }

                            binding.spVinType.setItem(vehicleTypeStringList)
                            try { binding.spVinType.setSelection(0) } catch (th: Throwable) {}
                            binding.spVinType.setOnItemSelectedListener(object : AdapterView.OnItemSelectedListener {
                                override fun onItemSelected(parent: AdapterView<*>?, view: View, position: Int, id: Long) {
                                    selectedvehicleType = vinTypeModelList[position].id
                                }
                                override fun onNothingSelected(parent: AdapterView<*>?) {}
                            })
                        }
                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Log.w("VEHICLE_TYPES", "Failed — ${anError.errorDetail}")
                            // ✅ Silently ignore — vehicle types not used in VMS
                        }
                    })
            }
        }.start()
    }

    // ✅ Changed getAsObjectList → getAsJSONObject, parse wrapped { "status", "data": [] }
    private fun invokeGetActiveCountryApi() {
        val pDialog = AwesomeProgressDialog(this).apply {
            setCancelable(false); setTitle("Please wait"); setMessage(""); setColoredCircle(R.color.pherosi); show()
        }
        Thread {
            AndroidNetworking.initialize(applicationContext, SslUtils.trustAllClient())
            runOnUiThread {
                AndroidNetworking.get(Constants.getActiveCountry)
                    .setTag(Constants.getActiveCountry)
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            Log.d("COUNTRIES", response.toString())
                            countryModelList = ArrayList()
                            countryStringList = ArrayList()

                            // ✅ Parse wrapped { "status": "success", "data": [...] }
                            val dataArray = response.optJSONArray("data")
                            if (dataArray != null) {
                                for (i in 0 until dataArray.length()) {
                                    val obj = dataArray.optJSONObject(i) ?: continue
                                    val item = GetActiveCountryResponseItem()
                                    item.id = obj.optInt("id", -1)
                                    item.name = obj.optString("name", "")
                                    if (item.id > 0 && item.name.isNotEmpty()) {
                                        countryModelList.add(item)
                                        countryStringList.add(item.name)
                                    }
                                }
                            }

                            binding.spCountry.setItem(countryStringList)

                            if (!StaticData.isForeigner) {
                                // ✅ Local — auto select first (Malaysia)
                                try { binding.spCountry.setSelection(0) } catch (th: Throwable) {}
                            } else {
                                // ✅ Foreigner — restore previously selected country if set
                                val savedCountryId = StaticData.request.passportIssueCountry
                                if (savedCountryId > 1) {
                                    val idx = countryModelList.indexOfFirst { it.id == savedCountryId }
                                    if (idx >= 0) {
                                        try {
                                            binding.spCountry.setSelection(idx)
                                            selectedCountry = savedCountryId
                                        } catch (th: Throwable) {}
                                    }
                                }
                                // else leave blank — user must pick
                            }

                            binding.spCountry.post { applyFontToSpinner(binding.spCountry) }

                            binding.spCountry.setOnItemSelectedListener(object : AdapterView.OnItemSelectedListener {
                                override fun onItemSelected(parent: AdapterView<*>?, view: View, position: Int, id: Long) {
                                    selectedCountry = countryModelList[position].id
                                    (view as? TextView)?.typeface = montserrat
                                }
                                override fun onNothingSelected(parent: AdapterView<*>?) {
                                    selectedCountry = -1
                                }
                            })
                        }
                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Common.showToast(applicationContext, "Failed to Load Countries", Common.ToastType.ERROR)
                        }
                    })
            }
        }.start()
    }

    private fun invokeCheckIcExist(icNumber: String) {
        val pDialog = AwesomeProgressDialog(this).apply {
            setCancelable(false); setTitle("Please wait"); setMessage(""); setColoredCircle(R.color.pherosi); show()
        }
        Thread {
            AndroidNetworking.initialize(applicationContext, SslUtils.trustAllClient())
            runOnUiThread {
                AndroidNetworking.post(Constants.getdetails)
                    .addJSONObjectBody(JSONObject().put("icNo", icNumber))
                    .setTag(Constants.getdetails)
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            Log.d("IC_CHECK_RESPONSE", response.toString())
                            try {
                                val dataObj = when {
                                    response.has("data") && !response.isNull("data") -> response.optJSONObject("data") ?: response
                                    else -> response
                                }
                                val photo = safeValue(dataObj.optString("profilePhotoPath", dataObj.optString("photo", "")))
                                val fullName = safeValue(dataObj.optString("visitorName", dataObj.optString("fullName", "")))
                                val contactNo = safeValue(dataObj.optString("phoneNo", dataObj.optString("contactNo", "")))
                                val email = safeValue(dataObj.optString("email", ""))
                                val address = safeValue(dataObj.optString("address", dataObj.optString("add1", "")))
                                val companyName = safeValue(dataObj.optString("companyName", dataObj.optString("company", "")))
                                val regNum = safeValue(dataObj.optString("vehicleNo", dataObj.optString("vehicleRegistration", dataObj.optString("regNum", ""))))

                                if (photo.isNotEmpty()) StaticData.base64_face = photo
                                if (fullName.isNotEmpty()) binding.etCardHolderName.setText(fullName)
                                if (contactNo.isNotEmpty()) binding.etContactNumber.setText(contactNo)
                                if (email.isNotEmpty()) binding.etEmail.setText(email)
                                if (address.isNotEmpty()) binding.etAddress.setText(address)
                                if (companyName.isNotEmpty()) binding.etCompanyName.setText(companyName)
                                if (regNum.isNotEmpty()) binding.etRegNo.setText(regNum)

                                if (fullName.isNotEmpty()) {
                                    Common.showToast(applicationContext, "Details Loaded Successfully", Common.ToastType.SUCCESS)
                                }
                            } catch (e: Exception) {
                                Log.e("PARSE_ERROR", "Error parsing response: ${e.message}")
                                Common.showToast(applicationContext, "Error Loading Details", Common.ToastType.ERROR)
                            }
                        }
                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Log.e("IC_CHECK_ERROR", "Error: ${anError.message}")
                            try {
                                val errorBody = anError.errorBody
                                val errorResponse = if (errorBody != null) JSONObject(errorBody) else null
                                val errorMessage = errorResponse?.optString("message", "") ?: ""
                                if (errorMessage.isNotBlank()) {
                                    Common.showToast(applicationContext, errorMessage, Common.ToastType.ERROR)
                                }
                            } catch (e: Exception) {
                                Log.e("IC_CHECK_ERROR", "Parse error: ${e.message}")
                            }
                        }
                    })
            }
        }.start()
    }

    private fun invokeCheckPassportExist(passportNumber: String) {
        val pDialog = AwesomeProgressDialog(this).apply {
            setCancelable(false); setTitle("Please wait"); setMessage(""); setColoredCircle(R.color.pherosi); show()
        }
        Thread {
            AndroidNetworking.initialize(applicationContext, SslUtils.trustAllClient())
            runOnUiThread {
                AndroidNetworking.post(Constants.getdetails)
                    .addJSONObjectBody(JSONObject().put("icNo", passportNumber))
                    .setTag(Constants.getdetails)
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            Log.d("PASSPORT_CHECK_RESPONSE", response.toString())
                            try {
                                val dataObj = when {
                                    response.has("data") && !response.isNull("data") -> response.optJSONObject("data") ?: response
                                    else -> response
                                }
                                val fullName = safeValue(dataObj.optString("visitorName", dataObj.optString("fullName", "")))
                                val contactNo = safeValue(dataObj.optString("phoneNo", dataObj.optString("contactNo", "")))
                                val email = safeValue(dataObj.optString("email", ""))
                                val companyName = safeValue(dataObj.optString("companyName", dataObj.optString("company", "")))
                                val regNum = safeValue(dataObj.optString("vehicleNo", dataObj.optString("vehicleRegistration", dataObj.optString("regNum", ""))))
                                val photo = safeValue(dataObj.optString("profilePhotoPath", dataObj.optString("photo", "")))

                                // ✅ Country auto-fill by name
                                val countryName = safeValue(dataObj.optString("country", dataObj.optString("passportIssuedCountry", "")))
                                if (countryName.isNotBlank() && ::countryModelList.isInitialized && countryModelList.isNotEmpty()) {
                                    val idx = countryModelList.indexOfFirst {
                                        it.name.trim().equals(countryName.trim(), ignoreCase = true)
                                    }
                                    if (idx >= 0) {
                                        try {
                                            binding.spCountry.setSelection(idx)
                                            selectedCountry = countryModelList[idx].id
                                            StaticData.request.passportIssueCountry = selectedCountry
                                            Log.d("COUNTRY_MATCH", "Matched: $countryName → idx=$idx id=$selectedCountry")
                                        } catch (th: Throwable) {
                                            Log.w("COUNTRY_MATCH", "setSelection failed: ${th.message}")
                                        }
                                    } else {
                                        Log.w("COUNTRY_MATCH", "No match for: $countryName")
                                    }
                                }

                                if (fullName.isNotEmpty()) binding.etCardHolderName.setText(fullName)
                                if (contactNo.isNotEmpty()) binding.etContactNumber.setText(contactNo)
                                if (email.isNotEmpty()) binding.etEmail.setText(email)
                                if (companyName.isNotEmpty()) binding.etCompanyName.setText(companyName)
                                if (regNum.isNotEmpty()) binding.etRegNo.setText(regNum)
                                if (photo.isNotEmpty()) StaticData.base64_face = photo

                                if (fullName.isNotEmpty()) {
                                    Common.showToast(applicationContext, "Passport Details Loaded", Common.ToastType.SUCCESS)
                                }
                            } catch (e: Exception) {
                                Log.e("PASSPORT_PARSE_ERROR", "Error: ${e.message}")
                                Common.showToast(applicationContext, "Error Loading Details", Common.ToastType.ERROR)
                            }
                        }
                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Log.e("PASSPORT_CHECK_ERROR", "Error: ${anError.message}")
                        }
                    })
            }
        }.start()
    }

    fun onReadMyKad() {
        if (morphoSmart == null) {
            val usbManager = baseContext.getSystemService("usb") as UsbManager
            if (deviceProbe == null) { MsgBox("No smart card reader attached to the system"); return }
            if (deviceProbe!!.usbDevice == null) { MsgBox("No smart card reader attached to the system"); return }
            morphoSmart = MorphoSmart(usbManager, deviceProbe!!.usbDevice, this)
        }
        try {
            morphoSmart!!.open()
            readCardAsyncManager!!.setupTask(ReadCardTask(resources, morphoSmart, true))
        } catch (e: DeviceException) {
            e.printStackTrace()
            MsgBox("Error opening smartcard reader")
        }
    }

    override fun onTaskComplete(task: ReadCardTask) {
        try {
            val readCardResult = task.get()
            if (readCardResult.isSuccessful == true) {
                val cardHolderInfo: CardHolderInfo = readCardResult.personalInfo
                binding.etCardHolderName.setText(cardHolderInfo.name)
                binding.etCardNumber.setText(cardHolderInfo.nric)
                binding.etAddress.setText("${cardHolderInfo.address1}, ${cardHolderInfo.address2}, ${cardHolderInfo.address3}")
                binding.spGender.setSelection(if (cardHolderInfo.gender == "M") 0 else 1)
                binding.ivCard.setImageBitmap(BitmapFactory.decodeByteArray(cardHolderInfo.photo, 0, cardHolderInfo.photo.size))
            } else {
                MsgBox("Failed to read MyKad")
            }
        } catch (e: Exception) {
            MsgBox(e.message)
        }
    }

    override fun onTaskComplete(task: VerifyFPTask) {
        try {
            try {
                val result = task.get()
                val morphoResult = result.morphoSmartResult
                when {
                    morphoResult.errorCode == 0 -> MsgBox(if (morphoResult.resultCode == 1) "Fingerprint matches fingerprint in MyKad" else "Fingerprint does not match fingerprint in MyKad")
                    morphoResult.errorCode == -3 -> MsgBox("Invalid fingerprint miniature")
                    morphoResult.errorCode == -6 -> MsgBox("Fingerprint verification operation timed out")
                    morphoResult.errorCode == -27 -> MsgBox("Fingerprint verification operation aborted")
                    morphoResult.errorCode == -100 -> MsgBox(result.errorMessage)
                    morphoResult.errorCode == -101 -> MsgBox("Fingerprint SDK activation failed. Make sure tablet is connected to internet.")
                    else -> MsgBox("Fingerprint verification operation encountered an error")
                }
                if (morphoSmart == null) return
            } catch (e: Exception) {
                e.printStackTrace()
                if (morphoSmart == null) return
            }
            morphoSmart!!.close()
        } catch (th: Throwable) {
            morphoSmart?.close()
            throw th
        }
    }

    override fun onTaskComplete(task: ReadCardVerifyFpTask) {
        try {
            val readCardResult = task.get()
            if (readCardResult.readCardResult.isSuccessful == true) {
                val cardHolderInfo = readCardResult.readCardResult.personalInfo
                val morphoResult = readCardResult.verifyFPResult.morphoSmartResult
                val verifyFpResult = when {
                    morphoResult.errorCode == 0 -> if (morphoResult.resultCode == 1) "Fingerprint matches fingerprint in MyKad" else "Fingerprint does not match fingerprint in MyKad"
                    morphoResult.errorCode == -3 -> "Invalid fingerprint miniature"
                    morphoResult.errorCode == -6 -> "Fingerprint verification operation timed out"
                    morphoResult.errorCode == -27 -> "Fingerprint verification operation aborted"
                    morphoResult.errorCode == -100 -> readCardResult.verifyFPResult.errorMessage
                    morphoResult.errorCode == -101 -> "Fingerprint SDK activation failed. Make sure tablet is connected to internet"
                    else -> if (morphoResult.errorCode == -101) "Fingerprint SDK activation failed due to invalid or missing license" else "Fingerprint verification operation encountered an error"
                }
                MsgBox("Fingerprint Verification Result: $verifyFpResult\n${Gson().toJson(cardHolderInfo)}")
            } else {
                MsgBox("Failed to read MyKad")
            }
        } catch (e: Exception) {
            MsgBox(e.message)
        }
    }

    fun MsgBox(response: String?) {
        AlertDialog.Builder(this)
            .setCancelable(false)
            .setMessage(response)
            .setNegativeButton("OK") { dialog, _ -> dialog.cancel() }
            .create().show()
    }
}