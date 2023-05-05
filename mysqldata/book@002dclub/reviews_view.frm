TYPE=VIEW
query=select `book-club`.`reviews`.`review_key` AS `review_key`,`book-club`.`reviews`.`account_key` AS `account_key`,`book-club`.`reviews`.`item_key` AS `item_key`,`book-club`.`reviews`.`header` AS `header`,`book-club`.`reviews`.`body` AS `body`,`book-club`.`reviews`.`stars` AS `stars`,`book-club`.`reviews`.`created_at` AS `created_at`,concat(`book-club`.`accounts`.`name_first`,\' \',`book-club`.`accounts`.`name_last`) AS `name`,`book-club`.`items`.`item_title` AS `title` from ((`book-club`.`reviews` join `book-club`.`accounts` on(`book-club`.`reviews`.`account_key` = `book-club`.`accounts`.`account_key`)) join `book-club`.`items` on(`book-club`.`reviews`.`item_key` = `book-club`.`items`.`item_key`))
md5=26bdf28f047f7cca4e1e759fd397a57d
updatable=1
algorithm=0
definer_user=root
definer_host=localhost
suid=2
with_check_option=0
timestamp=0001683245580513964
create-version=2
source=SELECT reviews.*,\nCONCAT(accounts.name_first, \' \', accounts.name_last) AS name,\nitems.item_title AS title \nFROM reviews JOIN accounts ON reviews.account_key = accounts.account_key JOIN items ON reviews.item_key = items.item_key
client_cs_name=utf8mb4
connection_cl_name=utf8mb4_unicode_ci
view_body_utf8=select `book-club`.`reviews`.`review_key` AS `review_key`,`book-club`.`reviews`.`account_key` AS `account_key`,`book-club`.`reviews`.`item_key` AS `item_key`,`book-club`.`reviews`.`header` AS `header`,`book-club`.`reviews`.`body` AS `body`,`book-club`.`reviews`.`stars` AS `stars`,`book-club`.`reviews`.`created_at` AS `created_at`,concat(`book-club`.`accounts`.`name_first`,\' \',`book-club`.`accounts`.`name_last`) AS `name`,`book-club`.`items`.`item_title` AS `title` from ((`book-club`.`reviews` join `book-club`.`accounts` on(`book-club`.`reviews`.`account_key` = `book-club`.`accounts`.`account_key`)) join `book-club`.`items` on(`book-club`.`reviews`.`item_key` = `book-club`.`items`.`item_key`))
mariadb-version=100428
