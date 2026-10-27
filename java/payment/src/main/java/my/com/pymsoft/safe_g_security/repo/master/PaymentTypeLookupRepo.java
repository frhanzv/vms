package my.com.pymsoft.safe_g_security.repo.master;

import java.util.List;

import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import my.com.pymsoft.safe_g_security.domain.master.PaymentTypeLookup;

public interface PaymentTypeLookupRepo extends JpaRepository<PaymentTypeLookup, Integer> {

	@Query("SELECT c FROM PaymentTypeLookup c " +
            "WHERE LOWER(c.type) like LOWER(:searchText) " +
            "OR LOWER(c.period) like LOWER(:searchText) " +
            "ORDER BY c.type ")
    Page<PaymentTypeLookup> searchPaymentTypeListPageNameAZ(@Param("searchText") String searchText, Pageable pageable);
	
	@Query("SELECT c FROM PaymentTypeLookup c " +
            "WHERE LOWER(c.type) like LOWER(:searchText) " +
            "OR LOWER(c.period) like LOWER(:searchText) " +
            "ORDER BY c.type DESC ")
    Page<PaymentTypeLookup> searchPaymentTypeListPageNameZA(@Param("searchText") String searchText, Pageable pageable);
	
	@Query("SELECT c FROM PaymentTypeLookup c " +
            "WHERE LOWER(c.type) like LOWER(:searchText) " +
            "OR LOWER(c.period) like LOWER(:searchText) " +
            "OR LOWER(c.amount) like LOWER(:searchText) " +
            "ORDER BY c.id desc ")
    Page<PaymentTypeLookup> searchPaymentTypeListPage(@Param("searchText") String searchText, Pageable pageable);

	@Query("SELECT c FROM PaymentTypeLookup c " +
            "WHERE c.type IN :typeList and c.status.statusId = 100001 ")
    List<PaymentTypeLookup> findPaymentByType(
            @Param("typeList") List<String> typeList
    );
}
