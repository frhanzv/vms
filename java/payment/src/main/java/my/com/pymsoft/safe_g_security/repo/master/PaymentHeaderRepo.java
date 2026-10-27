package my.com.pymsoft.safe_g_security.repo.master;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import my.com.pymsoft.safe_g_security.domain.master.PaymentHeader;

public interface PaymentHeaderRepo extends JpaRepository<PaymentHeader, Integer> {
	
	@Query("SELECT MAX(p.receiptNo) FROM PaymentHeader p")
	String findMaxReceiptNo();

	PaymentHeader findByVpId(Integer vp_id);

	PaymentHeader findByReceiptNo(String receiptNo);

	@Query("SELECT p.receiptNo FROM PaymentHeader p WHERE p.paymentStatus IS NULL OR LOWER(p.paymentStatus) <> 'paid'")
	List<String> getUnPaidReceiptNos();
	
	@Query("SELECT p.receiptNo FROM PaymentHeader p WHERE p.paymentStatus = 'paid' AND p.receiptNo=:receiptNo")
	List<String> getPaidReceiptNos(@Param("receiptNo") String receiptNo);

}
