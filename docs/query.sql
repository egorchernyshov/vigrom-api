select sum(subselect.values)
from (select value as values
      from account_histories
      where change_reason = 'refund'
        and transaction_type = 'credit'
        and created_at > current_date - interval '7' day
     ) subselect
;