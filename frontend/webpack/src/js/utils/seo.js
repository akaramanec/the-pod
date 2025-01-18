//UTM Cookie
let url = window.location.search;
let params = url === '' ? '' : url;

function utmParams(param) {
  param.slice(1).split('&').map((e) => {
    document.cookie = `${e.split('=')[0]}=${e.split('=')[1]}`;
  });
}
if (params) {
  utmParams(params);
}
