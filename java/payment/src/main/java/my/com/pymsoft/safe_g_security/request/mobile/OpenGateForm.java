package my.com.pymsoft.safe_g_security.request.mobile;

import java.io.Serializable;
import java.util.List;

public class OpenGateForm implements Serializable {

	/**
	 *
	 */
	private static final long serialVersionUID = 1L;

	private String laneNo = "";
	private Integer gateId = 0;
	private boolean jetson; // web & true - driver , false - passenger
	private String reason;
	private boolean manual;

	private Integer id;
	private String laneId;
	private String laneName;
	private String laneLocation;
	private List<String> cardNos;

	public String getLaneNo() {
		return laneNo;
	}

	public void setLaneNo(String laneNo) {
		this.laneNo = laneNo;
	}

	public Integer getGateId() {
		return gateId;
	}

	public void setGateId(Integer gateId) {
		this.gateId = gateId;
	}

	public Integer getId() {
		return id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public String getLaneId() {
		return laneId;
	}

	public void setLaneId(String laneId) {
		this.laneId = laneId;
	}

	public String getLaneName() {
		return laneName;
	}

	public void setLaneName(String laneName) {
		this.laneName = laneName;
	}

	public String getLaneLocation() {
		return laneLocation;
	}

	public void setLaneLocation(String laneLocation) {
		this.laneLocation = laneLocation;
	}

	public boolean isJetson() {
		return jetson;
	}

	public void setJetson(boolean jetson) {
		this.jetson = jetson;
	}

	public String getReason() {
		return reason;
	}

	public void setReason(String reason) {
		this.reason = reason;
	}

	public boolean isManual() {
		return manual;
	}

	public void setManual(boolean manual) {
		this.manual = manual;
	}

	public List<String> getCardNos() {
		return cardNos;
	}

	public void setCardNos(List<String> cardNos) {
		this.cardNos = cardNos;
	}
	
	

}
