package com.safeg.activities

import android.content.Intent
import android.graphics.Typeface
import android.os.Bundle
import android.util.Log
import android.view.View
import android.view.ViewGroup
import android.widget.AdapterView
import android.widget.ArrayAdapter
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.res.ResourcesCompat
import androidx.recyclerview.widget.LinearLayoutManager
import com.androidnetworking.AndroidNetworking
import com.androidnetworking.common.Priority
import com.androidnetworking.error.ANError
import com.androidnetworking.interfaces.JSONObjectRequestListener
import com.awesomedialog.blennersilva.awesomedialoglibrary.AwesomeProgressDialog
import com.safeg.Constants
import com.safeg.R
import com.safeg.StaticData
import com.safeg.adapters.StaffAdapter
import com.safeg.databinding.ActivityVisitUpdatedDetailsBinding
import com.safeg.models.GetLocationResponseItem
import com.safeg.models.GetStaffPassByStaffNoOrNameResponseItem
import com.safeg.models.GetSubLocationResponseItem
import com.safeg.models.Reason
import com.safeg.utils.Common
import com.safeg.utils.SslUtils
import org.json.JSONObject

class VisitUpdateDetailsActivity : AppCompatActivity(), View.OnClickListener {

    lateinit var adapter: StaffAdapter
    private lateinit var binding: ActivityVisitUpdatedDetailsBinding
    lateinit var companyList: ArrayList<Company>
    lateinit var companyStringList: ArrayList<String>
    lateinit var locationModelList: ArrayList<GetLocationResponseItem>
    lateinit var locationStringList: ArrayList<String>
    private lateinit var montserrat: Typeface
    lateinit var reasonsList: ArrayList<Reason>
    lateinit var subLocationModelList: ArrayList<GetSubLocationResponseItem>
    lateinit var subLocationStringList: ArrayList<String>
    lateinit var visitorTypesList: ArrayList<Reason>

    var selectedLocation: Int = -1
    var selectedLocationn: String = ""
    var selectedReason: String = ""
    var selectedVisitorType: String = ""
    var selectedSubLocation: Int = -1

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        binding = ActivityVisitUpdatedDetailsBinding.inflate(layoutInflater)
        setContentView(binding.root)

        montserrat = ResourcesCompat.getFont(this, R.font.montserrat_bold) ?: Typeface.DEFAULT_BOLD

        binding.ivSearch.setOnClickListener {
            invokeGetVisitUpdateDetailsApi(binding.etSearch.text.toString())
        }

        binding.rlMainPage.setOnClickListener {
            finish()
            startActivity(Intent(applicationContext, WelcomeActivity::class.java))
        }

        binding.ivBack.setOnClickListener { finish() }

        binding.rlNext.setOnClickListener {
            if (selectedReason.isEmpty()) {
                Common.showToast(this, "Please Select a Reason", Common.ToastType.WARNING)
                return@setOnClickListener
            }
            if (selectedVisitorType == "") {
                Common.showToast(this, "Please Select Visitor Type", Common.ToastType.WARNING)
                return@setOnClickListener
            }
            StaticData.request.reason = selectedReason
            if (selectedVisitorType.isNotEmpty()) {
                StaticData.request.visitorTypeId = Integer.parseInt(selectedVisitorType)
            }
            finish()
            val intent = Intent(applicationContext, ThankYouActivity::class.java)
            intent.putExtra(ThankYouActivity.EXTRA_FLOW, ThankYouActivity.FLOW_WALKIN)
            startActivity(intent)
        }

        invokeGetReasonsApi()
        invokeGetVisitorTypesApi()
    }

    private fun applyMontserratToSpinner() {
        val spinners = listOf(binding.spReason, binding.spLocation, binding.spSubLocation, binding.spCompany)
        for (spinner in spinners) {
            try {
                var clazz: Class<*>? = spinner.javaClass
                while (clazz != null) {
                    for (field in clazz.declaredFields) {
                        field.isAccessible = true
                        try {
                            val value = field.get(spinner)
                            if (value is TextView) {
                                value.typeface = montserrat
                                value.textSize = 14f
                            }
                        } catch (ignored: Exception) {}
                    }
                    clazz = clazz.superclass
                }
            } catch (e: Exception) {
                Log.w("Font", "Could not apply font to spinner: ${e.message}")
            }
        }
    }

    private fun makeFontAdapter(items: ArrayList<String>): ArrayAdapter<String> {
        return object : ArrayAdapter<String>(
            this,
            android.R.layout.simple_spinner_item,
            items
        ) {
            override fun getView(position: Int, convertView: View?, parent: ViewGroup): View {
                val view = super.getView(position, convertView, parent)
                (view as? TextView)?.typeface = montserrat
                (view as? TextView)?.textSize = 14f
                return view
            }
            override fun getDropDownView(position: Int, convertView: View?, parent: ViewGroup): View {
                val view = super.getDropDownView(position, convertView, parent)
                (view as? TextView)?.typeface = montserrat
                (view as? TextView)?.textSize = 14f
                return view
            }
        }
    }

    private fun invokeGetVisitUpdateDetailsApi(search: String) {
        val pDialog = AwesomeProgressDialog(this).apply {
            setCancelable(false); setTitle("Please wait"); setMessage(""); setColoredCircle(R.color.pherosi); show()
        }
        Thread {
            AndroidNetworking.initialize(applicationContext, SslUtils.trustAllClient())
            runOnUiThread {
                AndroidNetworking.get(Constants.getStaffPassByStaffNoOrName)
                    .addQueryParameter("keyword", search)
                    .setTag(Constants.getStaffPassByStaffNoOrName)
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            Log.d("STAFF_SEARCH", response.toString())
                            val dataArray = response.optJSONArray("data")
                            val staffList = ArrayList<GetStaffPassByStaffNoOrNameResponseItem>()
                            if (dataArray != null && dataArray.length() > 0) {
                                for (i in 0 until dataArray.length()) {
                                    val obj = dataArray.optJSONObject(i) ?: continue
                                    val item = GetStaffPassByStaffNoOrNameResponseItem()
                                    item.name = obj.optString("fullName", obj.optString("name", ""))
                                    item.username = obj.optString("staffNo", obj.optString("username", ""))
                                    item.mobileNo = obj.optString("mobileNo", "")
                                    item.email = obj.optString("email", "")
                                    item.status = obj.optString("status", "")
                                    staffList.add(item)
                                }
                            }
                            if (staffList.isNotEmpty()) {
                                val staffAdapter = StaffAdapter(staffList) { staff ->
                                    StaticData.request.nameOfPersonVisited = staff.name
                                    StaticData.request.contactNoOfPersonVisited = staff.mobileNo
                                    StaticData.request.staffNo = staff.username
                                    binding.etSearch.setText(StaticData.request.nameOfPersonVisited)
                                }
                                binding.rvStaffList.layoutManager = LinearLayoutManager(this@VisitUpdateDetailsActivity)
                                binding.rvStaffList.adapter = staffAdapter
                                binding.llVisitDetails.visibility = View.VISIBLE
                            } else {
                                Common.showToast(applicationContext, "No Records Found", Common.ToastType.ERROR)
                            }
                        }
                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Common.showToast(applicationContext, "No Records Found", Common.ToastType.ERROR)
                            val staffList = mutableListOf(
                                GetStaffPassByStaffNoOrNameResponseItem(),
                                GetStaffPassByStaffNoOrNameResponseItem(),
                                GetStaffPassByStaffNoOrNameResponseItem()
                            )
                            val staffAdapter = StaffAdapter(staffList) { staff ->
                                staffList.remove(staff)
                                adapter.notifyDataSetChanged()
                            }
                            binding.rvStaffList.layoutManager = LinearLayoutManager(applicationContext)
                            binding.rvStaffList.adapter = staffAdapter
                        }
                    })
            }
        }.start()
    }

    override fun onClick(view: View?) {
        view?.id
    }

    // ✅ Changed getAsObjectList → getAsJSONObject, parse wrapped { "status", "data": [] }
    private fun invokeGetLocationApi() {
        val pDialog = AwesomeProgressDialog(this).apply {
            setCancelable(false); setTitle("Please wait"); setMessage(""); setColoredCircle(R.color.pherosi); show()
        }
        Thread {
            AndroidNetworking.initialize(applicationContext, SslUtils.trustAllClient())
            runOnUiThread {
                AndroidNetworking.get(Constants.getLocationAccessList)
                    .setTag(Constants.getLocationAccessList)
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            Log.d("LOCATIONS", response.toString())
                            locationModelList = ArrayList()
                            locationStringList = ArrayList()

                            // ✅ Parse wrapped { "status": "success", "data": [...] }
                            val dataArray = response.optJSONArray("data")
                            if (dataArray != null) {
                                for (i in 0 until dataArray.length()) {
                                    val obj = dataArray.optJSONObject(i) ?: continue
                                    val item = GetLocationResponseItem()
                                    item.id = obj.optInt("id", -1)
                                    // ✅ Controller returns "locationAccess" not "name"
                                    item.name = obj.optString("locationAccess", obj.optString("name", ""))
                                    if (item.id > 0 && item.name.isNotEmpty()) {
                                        locationModelList.add(item)
                                        locationStringList.add(item.name)
                                    }
                                }
                            }

                            binding.spLocation.setItem(locationStringList)
                            binding.spLocation.post { applyMontserratToSpinner() }
                            binding.spLocation.setOnItemSelectedListener(object : AdapterView.OnItemSelectedListener {
                                override fun onItemSelected(parent: AdapterView<*>?, view: View, position: Int, id: Long) {
                                    selectedLocation = locationModelList[position].id
                                    selectedLocationn = locationModelList[position].name
                                    invokeGetSubLocationsByLocationApi(locationModelList[position].id)
                                }
                                override fun onNothingSelected(parent: AdapterView<*>?) {}
                            })
                        }
                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Common.showToast(applicationContext, "Failed to Load Locations", Common.ToastType.ERROR)
                        }
                    })
            }
        }.start()
    }

    // ✅ Changed POST+getAsObjectList → GET+getAsJSONObject, parse wrapped { "status", "data": [] }
    private fun invokeGetSubLocationsByLocationApi(locationAccessId: Int) {
        val pDialog = AwesomeProgressDialog(this).apply {
            setCancelable(false); setTitle("Please wait"); setMessage(""); setColoredCircle(R.color.pherosi); show()
        }
        Thread {
            AndroidNetworking.initialize(applicationContext, SslUtils.trustAllClient())
            runOnUiThread {
                // ✅ Controller is GET /api/admin/subLocationAccess/active — no locationId filter
                // Filter by locationId client-side after fetching all
                AndroidNetworking.get(Constants.getActiveSubLocationAssetList)
                    .setTag(Constants.getActiveSubLocationAssetList)
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            Log.d("SUBLOCATIONS", response.toString())
                            subLocationModelList = ArrayList()
                            subLocationStringList = ArrayList()

                            // ✅ Parse wrapped { "status": "success", "data": [...] }
                            // ✅ Filter by locationId client-side
                            val dataArray = response.optJSONArray("data")
                            if (dataArray != null) {
                                for (i in 0 until dataArray.length()) {
                                    val obj = dataArray.optJSONObject(i) ?: continue
                                    val locId = obj.optInt("locationId", -1)
                                    if (locId != locationAccessId) continue  // ✅ filter by parent location
                                    val item = GetSubLocationResponseItem()
                                    item.id = obj.optInt("id", -1)
                                    // ✅ Controller returns "lane" not "name"
                                    item.name = obj.optString("lane", obj.optString("name", ""))
                                    if (item.id > 0 && item.name.isNotEmpty()) {
                                        subLocationModelList.add(item)
                                        subLocationStringList.add(item.name)
                                    }
                                }
                            }

                            binding.spSubLocation.setItem(subLocationStringList)
                            binding.spSubLocation.post { applyMontserratToSpinner() }
                            binding.spSubLocation.setOnItemSelectedListener(object : AdapterView.OnItemSelectedListener {
                                override fun onItemSelected(parent: AdapterView<*>?, view: View, position: Int, id: Long) {
                                    selectedSubLocation = subLocationModelList[position].id
                                }
                                override fun onNothingSelected(parent: AdapterView<*>?) {}
                            })
                        }
                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Common.showToast(applicationContext, "Failed to Load Sub Locations", Common.ToastType.ERROR)
                        }
                    })
            }
        }.start()
    }

    private fun invokeGetReasonsApi() {
        val pDialog = AwesomeProgressDialog(this).apply {
            setCancelable(false); setTitle("Please wait"); setMessage(""); setColoredCircle(R.color.pherosi); show()
        }
        Thread {
            AndroidNetworking.initialize(applicationContext, SslUtils.trustAllClient())
            runOnUiThread {
                AndroidNetworking.get(Constants.getVisitReasons)
                    .setTag(Constants.getVisitReasons)
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            Log.d("REASONS_RESPONSE", response.toString())
                            reasonsList = ArrayList()

                            // ✅ New format: { "content": [{ "id": 1, "reason": "..." }] }
                            val contentArray = response.optJSONArray("content")
                            if (contentArray != null && contentArray.length() > 0) {
                                for (i in 0 until contentArray.length()) {
                                    val obj = contentArray.optJSONObject(i) ?: continue
                                    val name = obj.optString("reason", obj.optString("name", ""))
                                    val id = obj.optInt("id", -1)
                                    if (name.isNotEmpty() && id > 0) {
                                        val a = Reason()
                                        a.id = id.toString()
                                        a.name = name
                                        reasonsList.add(a)
                                    }
                                }
                            } else {
                                // ✅ Fallback old format
                                val listArray = response.optJSONArray("list")
                                if (listArray != null && listArray.length() > 0) {
                                    for (i in 0 until listArray.length()) {
                                        val innerArray = listArray.optJSONArray(i)
                                        if (innerArray != null && innerArray.length() > 0) {
                                            for (j in 0 until innerArray.length()) {
                                                val obj = innerArray.optJSONObject(j) ?: continue
                                                val name = obj.optString("name", "")
                                                val id = obj.optString("id", "")
                                                if (name.isNotEmpty()) {
                                                    val a = Reason()
                                                    a.id = id
                                                    a.name = name
                                                    reasonsList.add(a)
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            val nameList = ArrayList<String>()
                            for (r in reasonsList) nameList.add(r.name)
                            val reasonAdapter = makeFontAdapter(nameList)
                            binding.spReason.adapter = reasonAdapter
                            binding.spReason.post { applyMontserratToSpinner() }
                            binding.spReason.setOnItemSelectedListener(object : AdapterView.OnItemSelectedListener {
                                override fun onItemSelected(parent: AdapterView<*>?, view: View, position: Int, id: Long) {
                                    selectedReason = reasonsList[position].id
                                    (view as? TextView)?.typeface = montserrat
                                }
                                override fun onNothingSelected(parent: AdapterView<*>?) {}
                            })
                        }
                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Common.showToast(applicationContext, "Failed to Load Reasons", Common.ToastType.ERROR)
                        }
                    })
            }
        }.start()
    }

    private fun invokeGetVisitorTypesApi() {
        val pDialog = AwesomeProgressDialog(this).apply {
            setCancelable(false); setTitle("Please wait"); setMessage(""); setColoredCircle(R.color.pherosi); show()
        }
        Thread {
            AndroidNetworking.initialize(applicationContext, SslUtils.trustAllClient())
            runOnUiThread {
                AndroidNetworking.get(Constants.visitorTypes)
                    .setTag(Constants.visitorTypes)
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            Log.d("VISITOR_TYPES_RESPONSE", response.toString())
                            visitorTypesList = ArrayList()
                            val dataArray = response.optJSONArray("data")
                            if (dataArray != null && dataArray.length() > 0) {
                                for (j in 0 until dataArray.length()) {
                                    val obj = dataArray.optJSONObject(j) ?: continue
                                    val name = obj.optString("name", "").ifBlank {
                                        obj.optString("visitor_type", "")
                                    }
                                    val id = obj.optString("id", "")
                                    if (name.isNotEmpty()) {
                                        val a = Reason()
                                        a.id = id
                                        a.name = name
                                        visitorTypesList.add(a)
                                    }
                                }
                            }
                            val nameList = ArrayList<String>()
                            for (r in visitorTypesList) nameList.add(r.name)
                            val visitorAdapter = makeFontAdapter(nameList)
                            binding.spLocation.adapter = visitorAdapter
                            binding.spLocation.post { applyMontserratToSpinner() }
                            binding.spLocation.setOnItemSelectedListener(object : AdapterView.OnItemSelectedListener {
                                override fun onItemSelected(parent: AdapterView<*>?, view: View, position: Int, id: Long) {
                                    selectedVisitorType = visitorTypesList[position].id
                                    (view as? TextView)?.typeface = montserrat
                                }
                                override fun onNothingSelected(parent: AdapterView<*>?) {}
                            })
                        }
                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Common.showToast(applicationContext, "Failed to Load Visitor Types", Common.ToastType.ERROR)
                        }
                    })
            }
        }.start()
    }

    private fun invokeGetCompaniesApi() {
        val pDialog = AwesomeProgressDialog(this).apply {
            setCancelable(false); setTitle("Please wait"); setMessage(""); setColoredCircle(R.color.pherosi); show()
        }
        Thread {
            AndroidNetworking.initialize(applicationContext, SslUtils.trustAllClient())
            runOnUiThread {
                AndroidNetworking.get(Constants.getStaffPassByStaffNoOrName)
                    .setTag(Constants.getStaffPassByStaffNoOrName)
                    .setPriority(Priority.HIGH)
                    .build()
                    .getAsJSONObject(object : JSONObjectRequestListener {
                        override fun onResponse(response: JSONObject) {
                            pDialog.hide()
                            companyList = ArrayList()
                            companyStringList = ArrayList()
                            val dataArray = response.optJSONArray("data")
                            if (dataArray != null) {
                                for (i in 0 until dataArray.length()) {
                                    val obj = dataArray.optJSONObject(i) ?: continue
                                    val item = Company()
                                    item.id = obj.optString("id", "")
                                    item.name = obj.optString("name", "")
                                    if (item.name.isNotEmpty()) {
                                        companyList.add(item)
                                        companyStringList.add(item.name)
                                    }
                                }
                            }
                            binding.spCompany.setItem(companyStringList)
                            binding.spCompany.setOnItemClickListener { _, _, position, _ ->
                                StaticData.request.contactNoOfPersonVisited = companyList[position].contactNoOfPersonVisited
                                StaticData.request.nameOfPersonVisited = companyList[position].name
                                StaticData.request.cpnID = companyList[position].id
                                binding.llVisitDetails.visibility = View.VISIBLE
                            }
                        }
                        override fun onError(anError: ANError) {
                            pDialog.hide()
                            Common.showToast(applicationContext, "Failed to Load Companies", Common.ToastType.ERROR)
                        }
                    })
            }
        }.start()
    }
}