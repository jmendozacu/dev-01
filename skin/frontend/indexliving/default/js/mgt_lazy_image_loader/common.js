//$.noConflict();
jQuery(document).ready(function($) {
  jQuery('img.lazy').jail({
    event: 'load+scroll',
    placeholder : BASE_URL + "/skin/frontend/indexliving/default/images/mgt_lazy_image_loader/loader.gif",
  });
});