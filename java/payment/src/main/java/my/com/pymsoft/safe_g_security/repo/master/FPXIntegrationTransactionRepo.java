package my.com.pymsoft.safe_g_security.repo.master;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import my.com.pymsoft.safe_g_security.domain.master.FPXIntegrationTransaction;

public interface FPXIntegrationTransactionRepo extends JpaRepository<FPXIntegrationTransaction,Integer>{

	@Query("SELECT o FROM FPXIntegrationTransaction o " +
            "WHERE LOWER(o.receiptNo) = LOWER(:receiptNo) ")
	List<FPXIntegrationTransaction> findByReceiptNo(@Param("receiptNo")String receiptNo);

}
