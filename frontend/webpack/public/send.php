<?php /** @noinspection ALL */


$admin_email = '';

$massage = '';
function adopt($text)
{
  return '=?UTF-8?B?' . Base64_encode($text) . '?=';
}

function br2nl($input)
{
  return preg_replace('/<br\s?\/?>/ius', "\n", str_replace("\n", "", str_replace("\r", "", htmlspecialchars_decode($input))));
}

$headers = "MIME-Version: 1.0" . PHP_EOL .
  "Content-Type: resources/html; charset=utf-8" . PHP_EOL .
  'From: ' . adopt("AmemoryPro") . ' <' . $admin_email . '>' . PHP_EOL .
  'Reply-To: ' . $admin_email . '' . PHP_EOL;


if (isset($_POST)) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['tel'];
  $description = $_POST['message'];

  $massage =
    'Номер телефона: ' . $phone . '<br>';
  if (isset($_POST['email'])) {
    $massage .=
      'От: ' . $name . '<br>' .
      'EMail: ' . $email . '<br>' .
      'Описание: ' . $description . '<br>';
  }
} else {
  return 'Error';
}


mail($admin_email, adopt($form_subject), $massage, $headers);
header('Location: https://boto.agency/');
exit;
