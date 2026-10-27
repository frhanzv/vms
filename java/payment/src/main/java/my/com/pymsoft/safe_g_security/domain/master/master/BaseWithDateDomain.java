package my.com.pymsoft.safe_g_security.domain.master.master;

import com.fasterxml.jackson.annotation.JsonView;
import my.com.pymsoft.safe_g_security.view.View;

import javax.persistence.Column;
import javax.persistence.MappedSuperclass;
import javax.persistence.Temporal;
import javax.persistence.TemporalType;
import java.io.Serializable;
import java.util.Date;

@MappedSuperclass
public class BaseWithDateDomain implements Serializable {

    private static final long serialVersionUID = 3178939338874879300L;

    @JsonView({View.PortPass.class,View.TrainingDetails.class,View.PortPassReprintHistoryList.class,View.CompanyPermit.class,View.CompanyPermitList.class,View.UserList.class,View.PortPassCard.class,View.BlacklistList.class,View.Blacklist.class
     ,View.PortPassMonthlyReportList.class,View.PortPassChangeCompanyHistory.class,View.VehicleReissueHistoryList.class,View.VehicleList.class,View.Vehicle.class,View.VisitorInvList.class,View.PortPassReceiptHistoryList.class,
     View.VehicleAllocationList.class,View.PTWPageList.class, View.PTWInspectionList.class,View.PTWAppealList.class, View.AWPListLimited.class, View.AWP.class, View.Groups.class, View.OnlineTraining.class, View.BranchOffice.class ,View.DoorINFO.class,
     View.VisitReason.class,View.VehicleInfoList.class,View.LaneList.class})
    @Temporal(TemporalType.TIMESTAMP)
    @Column(name = "CREATEDDATE", nullable = false,columnDefinition="TIMESTAMP")
    private Date createdDate;

    @JsonView({View.PortPassIssuanceList.class,View.PortPassClosedList.class,View.PortPass.class,View.PortPassCard.class,View.VehicleReissueHistoryList.class,View.VehicleList.class,View.VehicleAllocationList.class,
            View.PTWPageList.class, View.PTWInspectionList.class, View.AWPListLimited.class, View.AWP.class, View.Groups.class, View.OnlineTraining.class,View.SummonList.class,View.Summon.class, View.BranchOffice.class,
            View.DoorINFO.class,View.VisitReason.class,View.LaneList.class})
    @Temporal(TemporalType.TIMESTAMP)
    @Column(name = "UPDATEDDATE",columnDefinition="TIMESTAMP")
    private Date updatedDate;

    public Date getCreatedDate() {
        return createdDate;
    }

    public void setCreatedDate(Date createdDate) {
        this.createdDate = createdDate;
    }

    public Date getUpdatedDate() {
        return updatedDate;
    }

    public void setUpdatedDate(Date updatedDate) {
        this.updatedDate = updatedDate;
    }
}
