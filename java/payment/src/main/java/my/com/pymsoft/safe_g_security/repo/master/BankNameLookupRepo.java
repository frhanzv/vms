package my.com.pymsoft.safe_g_security.repo.master;

import my.com.pymsoft.safe_g_security.domain.master.BankNameLookup;
import my.com.pymsoft.safe_g_security.domain.master.master.Status;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import java.util.List;

public interface BankNameLookupRepo extends JpaRepository<BankNameLookup, Integer> {

    @Query("SELECT c FROM BankNameLookup c " +
            "WHERE c.status IN :statusList ")
    List<BankNameLookup> findAllStatus(
            @Param("statusList") List<Status> statusList
    );

    @Query("SELECT s FROM BankNameLookup s " +
            "WHERE LOWER(s.name) like LOWER(:searchText) " +
            "ORDER BY s.name,s.id ")
    Page<BankNameLookup> getBankNameListPage(@Param("searchText") String searchText, Pageable pageable);

}
