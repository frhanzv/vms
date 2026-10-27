package my.com.pymsoft.safe_g_security.repo.master;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import my.com.pymsoft.safe_g_security.domain.master.FPXIntegrationTransaction;
import my.com.pymsoft.safe_g_security.domain.master.FpxTransaction;

public interface FpxTransactionRepo extends JpaRepository<FpxTransaction,Integer>{
	
	@Query("SELECT o FROM FpxTransaction o " +
            "WHERE LOWER(o.fpxSellerOrderNo) = LOWER(:receiptNo) ")
	List<FpxTransaction> findBySellerOrderNo(@Param("receiptNo")String receiptNo);


}
