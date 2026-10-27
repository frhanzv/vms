package my.com.pymsoft.safe_g_security.domain.master;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.FetchType;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.JoinColumn;
import javax.persistence.ManyToOne;
import javax.persistence.SequenceGenerator;
import javax.persistence.Table;

import com.fasterxml.jackson.annotation.JsonView;

import my.com.pymsoft.safe_g_security.domain.master.master.BaseWithDateDomain;
import my.com.pymsoft.safe_g_security.domain.master.master.Status;
import my.com.pymsoft.safe_g_security.view.View;

@Entity
@Table(name = "SAFEG_PAYMENT_TYPE_LOOKUP")
public class PaymentTypeLookup extends BaseWithDateDomain {

    @JsonView(View.PaymentTypeList.class)
    @Column(name="ID",columnDefinition="NUMBER")
    @Id
    @SequenceGenerator(initialValue=1, sequenceName="PAYMENT_TYPE_LOOKUP_SEQ",allocationSize=1,name="payment_type_lookup_seq")
    @GeneratedValue(generator="payment_type_lookup_seq",strategy=GenerationType.SEQUENCE)
    private Integer id;

    @JsonView(View.PaymentTypeList.class)
    @Column(name = "TYPE", nullable = true, length = 255)
    private String type;

    @JsonView(View.PaymentTypeList.class)
    @Column(name = "PERIOD", nullable = true, length = 255)
    private String period;

    @JsonView(View.PaymentTypeList.class)
    @Column(name = "AMOUNT",columnDefinition="NUMBER")
    private Integer amount;   

    @JsonView(View.PaymentTypeList.class)
    @JoinColumn(name="STATUSID")
    @ManyToOne(optional = false, fetch = FetchType.LAZY)
    private Status status;

	public Integer getId() {
		return id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public String getType() {
		return type;
	}

	public void setType(String type) {
		this.type = type;
	}

	public String getPeriod() {
		return period;
	}

	public void setPeriod(String period) {
		this.period = period;
	}

	public Integer getAmount() {
		return amount;
	}

	public void setAmount(Integer amount) {
		this.amount = amount;
	}

	public Status getStatus() {
		return status;
	}

	public void setStatus(Status status) {
		this.status = status;
	}

   
}