function jsUpdateCart(){
  var parameter_string = '';
  allNodes = document.getElementsByClassName("process");
  for(i = 0; i < allNodes.length; i++) {
  	var tempid = allNodes[i].id;
    var temp = new Array;
    temp = tempid.split("_");
 	var real_id = temp[2];
 	var real_value = allNodes[i].value;
    parameter_string += real_id +':'+real_value+',';
  }

  var params = 'ids='+parameter_string;
  var ajax = new Ajax.Updater(
    'ajax_msg','/welcome/ajax_cart', {method:'post',parameters:params,onComplete:showMessage}
    );

}

function showMessage(req){
  $('ajax_msg').innerHTML = req.responseText;
  location.reload(true);
}


function jsRemoveProduct(id){
  var params = 'id='+id;
  var ajax = new Ajax.Updater(
    'ajax_msg','/welcome/ajax_cart_remove', {method:'post',parameters:params,onComplete:showMessage}
    );

}