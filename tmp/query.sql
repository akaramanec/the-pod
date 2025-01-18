SELECT `avpml`.* FROM `shop_attribute_value_product_mod_link` `avpml`
LEFT JOIN `shop_attribute_value` ON `avpml`.`attribute_value_id` = `shop_attribute_value`.`id`
LEFT JOIN `shop_attribute` `shopAttribute` ON `shop_attribute_value`.`attribute_id` = `shopAttribute`.`id`
WHERE `avpml`.`mod_id` IN (52, 28, 16, 47, 37, 41, 8, 23, 49, 35, 13, 36, 29, 48, 7, 18, 45, 27, 33, 25, 31, 22, 46, 21, 2, 40, 5, 26, 10, 42, 30, 6, 34, 15, 17, 44, 12, 38, 3, 20, 24, 50, 32, 39, 14, 51, 4, 19, 11, 43, 9)
GROUP BY `avpml`.`attribute_value_id`
ORDER BY `shopAttribute`.`sort`, `attributeValue`.`sort`
