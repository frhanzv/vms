package my.com.pymsoft.safe_g_security.domain.master;


import java.math.BigDecimal;
import java.time.LocalDateTime;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.Lob;
import javax.persistence.SequenceGenerator;
import javax.persistence.Table;

import lombok.Getter;
import lombok.Setter;

@Getter
@Setter
@Entity
@Table(name = "FPX_TRANSACTION")
public class FpxTransaction {

	@Id
	@GeneratedValue(strategy = GenerationType.SEQUENCE, generator = "fpx_seq")
	@SequenceGenerator(name = "fpx_seq", sequenceName = "FPX_TRANSACTION_SEQ", allocationSize = 1)
	private Integer id;

    @Column(name = "fpx_credit_auth_no", length = 50)
    private String fpxCreditAuthNo;

    @Column(name = "fpx_msg_token", length = 10)
    private String fpxMsgToken;

    @Column(name = "fpx_txn_currency", length = 10)
    private String fpxTxnCurrency;

    @Column(name = "fpx_seller_order_no", length = 50)
    private String fpxSellerOrderNo;

    @Column(name = "fpx_credit_auth_code", length = 10)
    private String fpxCreditAuthCode;

    @Column(name = "fpx_txn_time")
    private String fpxTxnTime;

    @Column(name = "fpx_maker_name", length = 100)
    private String fpxMakerName;

    @Column(name = "fpx_debit_auth_no", length = 50)
    private String fpxDebitAuthNo;

    @Column(name = "fpx_txn_amount", precision = 15, scale = 2)
    private String fpxTxnAmount;

    @Column(name = "fpx_seller_ex_id", length = 50)
    private String fpxSellerExId;

    @Column(name = "fpx_buyer_bank_id", length = 20)
    private String fpxBuyerBankId;

    @Column(name = "fpx_msg_type", length = 10)
    private String fpxMsgType;

    @Lob
    @Column(name = "fpx_check_sum")
    private String fpxCheckSum;

    @Column(name = "fpx_seller_ex_order_no", length = 50)
    private String fpxSellerExOrderNo;

    @Column(name = "fpx_buyer_name", length = 200)
    private String fpxBuyerName;

    @Column(name = "fpx_buyer_iban", length = 50)
    private String fpxBuyerIban;

    @Column(name = "fpx_seller_id", length = 50)
    private String fpxSellerId;

    @Column(name = "fpx_seller_txn_time")
    private String fpxSellerTxnTime;

    @Column(name = "fpx_buyer_id", length = 50)
    private String fpxBuyerId;

    @Column(name = "fpx_debit_auth_code", length = 10)
    private String fpxDebitAuthCode;

    @Column(name = "fpx_buyer_bank_branch", length = 100)
    private String fpxBuyerBankBranch;

    @Column(name = "fpx_fpx_txn_id", length = 50)
    private String fpxFpxTxnId;

    // getters & setters
}