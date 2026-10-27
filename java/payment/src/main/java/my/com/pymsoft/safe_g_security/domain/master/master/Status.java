package my.com.pymsoft.safe_g_security.domain.master.master;

import com.fasterxml.jackson.annotation.JsonView;
import my.com.pymsoft.safe_g_security.view.View;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.Id;
import javax.persistence.Table;
import java.io.Serializable;

@Entity
@Table(name = "SAFEG_STATUS")
public class Status implements Serializable {

	private static final long serialVersionUID = -1830976785812997430L;

	@JsonView({ View.RoleList.class, View.RoleDetails.class, View.CountryList.class, View.DepartmentList.class,
			View.LocationAccessList.class, View.StateList.class, View.CitiesList.class, View.RegistrationTypeList.class,
			View.BusinessTypeList.class, View.RejectReasonList.class, View.TrainingCalendarList.class,
			View.TrainingDetails.class, View.PortPassList.class, View.PortPass.class, View.PortPassIssuanceList.class,
			View.PortPassClosedList.class, View.VehicleTypeList.class, View.VehicleList.class, View.Vehicle.class,
			View.BankNameList.class, View.PortPassReprintHistoryListDetails.class, View.CategoriesList.class,
			View.CompanyPermit.class, View.CompanyPermitList.class, View.BlacklistList.class, View.Blacklist.class,
			View.UserList.class, View.LaneList.class, View.PortPassCard.class, View.LocationVisitedLookup.class,
			View.SubLocationAccessList.class, View.BlacklistReasonList.class, View.ReleaseReasonList.class,
			View.CargoList.class, View.WarehouseList.class, View.ReleaseReasonList.class,
			View.VehicleReissueHistoryList.class, View.VehicleReportList.class, View.DesignationList.class,
			View.LicenseClassList.class, View.VisitorCardList.class, View.SummonList.class, View.Summon.class,
			View.OperatingHours.class, View.PTWPageList.class, View.PTW.class, View.AWPListLimited.class,
			View.AWP.class, View.Questionlist.class, View.OffenceList.class, View.ShiftList.class,
			View.AddPermitTypeList.class, View.Evacuation.class, View.Groups.class, View.Groups.class,
			View.OnlineTraining.class, View.PTWAppealList.class, View.BranchOffice.class, View.PublicHoliday.class,
			View.SubCompanyList.class, View.SubLocationAccessSync.class, View.LocationAccessSync.class,
			View.PassDetailForGate.class, View.GateMobileResponse.class, View.SubCompanyList.class,
			View.UserSync.class,
			View.VendorPassSync.class, View.VendorPassUrineFileSync.class, View.VendorPassAppealSync.class,
			View.VendorPassAppealFileSync.class, View.VisitorPassReqLicenseSync.class,
			View.ClientVisitorInvPersonSync.class, View.ClientVisitorInvSync.class,
			View.ClientVisitorInvLocationAccessSync.class, View.ClientVisitorInvDateValiditySync.class,
			View.VisitorPassICSync.class, View.VisitorPassVaccineSync.class, View.VisitorPassFacialSync.class,
			View.VisitorPassAssetEquipmentSync.class, View.VendorPassDateExtensionSync.class, View.BlacklistSync.class,
			View.BlacklistFileReleaseSync.class, View.BlacklistFileSync.class, View.AdHocTrainingSync.class,
			View.VendorPassAdhocTrainingSync.class, View.VendorPassCardSync.class, View.VendorPassReceiptSync.class,
			View.VisitorPassCardSerialNoSync.class, View.VendorPassRStatusLogSync.class,
			View.VendorPassReprintFileSync.class, View.VendorPassChangeCompanyHistorySync.class,
			View.VisitorPassInvSync.class, View.VisitorPassReqSync.class, View.VisitorPassReqFileSync.class,
			View.VisitorPassReqDateValiditySync.class, View.VisitorPassReqLocationAccessSync.class,
			View.VisitorPassInvLocationSync.class, View.VisitorPassInvDateValiditySync.class,
			View.VendorPassUrineSync.class, View.VendorPassLocationAccessSync.class, View.VendorPassRTSync.class,
			View.VendorPassVaccineSync.class, View.VendorPassLicenseSync.class, View.VendorPassICSync.class,
			View.VendorPassTrainingSync.class, View.VendorPassFacialSync.class, View.VendorPassSerialVPCardSync.class,
			View.VendorPassQuesAssignSync.class, View.CompanyPermitMobile.class,View.CityLookupSync.class,View.StateLookupSync.class,
			View.CompanyPermitSync.class,View.LicenseClassLookupSync.class,
			View.DesignationLookupSync.class,View.RegistrationTypeSync.class,View.LaneSetupSync.class, View.DepartmentLookupSync.class,
			View.VendorPassReprintSync.class, View.VisitReasonSync.class, View.VehicleTypeLookupSync.class, View.TemporaryWorkerPassList.class, 
			View.PaymentTypeList.class, View.VehicleSync.class, View.IssuanceList.class, View.PaymentHeader.class, View.PaymentDetail.class})
	@Id
	@Column(name = "STATUSID", columnDefinition = "numeric(6)")
	private Integer statusId;

	@JsonView({ View.RoleList.class, View.RoleDetails.class, View.CountryList.class, View.DepartmentList.class,
		View.LocationAccessList.class, View.StateList.class, View.CitiesList.class, View.RegistrationTypeList.class,
		View.BusinessTypeList.class, View.RejectReasonList.class, View.TrainingCalendarList.class,
		View.TrainingDetails.class, View.PortPassList.class, View.PortPass.class, View.PortPassIssuanceList.class,
		View.PortPassClosedList.class, View.VehicleTypeList.class, View.VehicleList.class, View.Vehicle.class,
		View.BankNameList.class, View.PortPassReprintHistoryListDetails.class, View.CategoriesList.class,
		View.CompanyPermit.class, View.CompanyPermitList.class, View.BlacklistList.class, View.Blacklist.class,
		View.UserList.class, View.LaneList.class, View.PortPassCard.class, View.LocationVisitedLookup.class,
		View.SubLocationAccessList.class, View.BlacklistReasonList.class, View.ReleaseReasonList.class,
		View.CargoList.class, View.WarehouseList.class, View.ReleaseReasonList.class,
		View.VehicleReissueHistoryList.class, View.VehicleReportList.class, View.DesignationList.class,
		View.LicenseClassList.class, View.VisitorCardList.class, View.SummonList.class, View.Summon.class,
		View.OperatingHours.class, View.PTWPageList.class, View.PTW.class, View.AWPListLimited.class,
		View.AWP.class, View.Questionlist.class, View.OffenceList.class, View.ShiftList.class,
		View.AddPermitTypeList.class, View.Evacuation.class, View.Groups.class, View.Groups.class,
		View.OnlineTraining.class, View.PTWAppealList.class, View.BranchOffice.class, View.PublicHoliday.class,
		View.SubCompanyList.class, View.SubLocationAccessSync.class, View.LocationAccessSync.class,
		View.PassDetailForGate.class, View.GateMobileResponse.class, View.SubCompanyList.class,
		View.UserSync.class,
		View.VendorPassSync.class, View.VendorPassUrineFileSync.class, View.VendorPassAppealSync.class,
		View.VendorPassAppealFileSync.class, View.VisitorPassReqLicenseSync.class,
		View.ClientVisitorInvPersonSync.class, View.ClientVisitorInvSync.class,
		View.ClientVisitorInvLocationAccessSync.class, View.ClientVisitorInvDateValiditySync.class,
		View.VisitorPassICSync.class, View.VisitorPassVaccineSync.class, View.VisitorPassFacialSync.class,
		View.VisitorPassAssetEquipmentSync.class, View.VendorPassDateExtensionSync.class, View.BlacklistSync.class,
		View.BlacklistFileReleaseSync.class, View.BlacklistFileSync.class, View.AdHocTrainingSync.class,
		View.VendorPassAdhocTrainingSync.class, View.VendorPassCardSync.class, View.VendorPassReceiptSync.class,
		View.VisitorPassCardSerialNoSync.class, View.VendorPassRStatusLogSync.class,
		View.VendorPassReprintFileSync.class, View.VendorPassChangeCompanyHistorySync.class,
		View.VisitorPassInvSync.class, View.VisitorPassReqSync.class, View.VisitorPassReqFileSync.class,
		View.VisitorPassReqDateValiditySync.class, View.VisitorPassReqLocationAccessSync.class,
		View.VisitorPassInvLocationSync.class, View.VisitorPassInvDateValiditySync.class,
		View.VendorPassUrineSync.class, View.VendorPassLocationAccessSync.class, View.VendorPassRTSync.class,
		View.VendorPassVaccineSync.class, View.VendorPassLicenseSync.class, View.VendorPassICSync.class,
		View.VendorPassTrainingSync.class, View.VendorPassFacialSync.class, View.VendorPassSerialVPCardSync.class,
		View.VendorPassQuesAssignSync.class, View.CompanyPermitMobile.class,View.CityLookupSync.class,View.StateLookupSync.class,View.CompanyPermitSync.class,View.LicenseClassLookupSync.class,
		View.DesignationLookupSync.class,View.RegistrationTypeSync.class,View.LaneSetupSync.class, View.DepartmentLookupSync.class,
		View.VendorPassReprintSync.class, View.VisitReasonSync.class, View.VehicleTypeLookupSync.class, View.TemporaryWorkerPassList.class,
		View.PaymentTypeList.class , View.VehicleSync.class, View.IssuanceList.class, View.PaymentHeader.class, View.PaymentDetail.class})
	@Column(name = "NAME", length = 200)
	private String name;

	@JsonView({ View.SubLocationAccessSync.class, View.LocationAccessSync.class, View.DepartmentLookupSync.class,
		View.VendorPassReprintSync.class, View.VisitReasonSync.class, View.VehicleTypeLookupSync.class,  View.VehicleSync.class })
	@Column(name = "DESCRIPTION", length = 200)
	private String description;

	public Integer getStatusId() {
		return statusId;
	}

	public void setStatusId(Integer statusId) {
		this.statusId = statusId;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getDescription() {
		return description;
	}

	public void setDescription(String description) {
		this.description = description;
	}
}
