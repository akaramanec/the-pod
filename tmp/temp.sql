	
SELECT `customer`.`id`, `customer`.`last_name`, JSON_EXTRACT(`blog`.`cache`, '$.customerCount') AS customerCount
FROM `bot_customer` `customer`
LEFT JOIN `bot` `bot` ON `customer`.`bot_id` = `bot`.`id`
LEFT JOIN `bot_customer_blog` `blog` ON `customer`.`id` = `blog`.`customer_id`
LEFT JOIN `bot_customer` `parent` ON `customer`.`parent_id` = `parent`.`id`
WHERE (`customer`.`status`=3) AND (`customer`.`blogger`=3)
ORDER BY customerCount DESC
LIMIT 20

SELECT `customer`.id, `customer`.last_name, `blog`.cache->>'$.customerCount' AS customerCount FROM `bot_customer` `customer`
LEFT JOIN `bot` `bot` ON `customer`.`bot_id` = `bot`.`id`
LEFT JOIN `bot_customer_blog` `blog` ON `customer`.`id` = `blog`.`customer_id`
LEFT JOIN `bot_customer` `parent` ON `customer`.`parent_id` = `parent`.`id`
WHERE (`customer`.`status`=3) AND (`customer`.`blogger`=3)
ORDER BY customerCount DESC
LIMIT 20
