function toggleCalculator(){let widthProduct=document.querySelector('#woocommerce-correios-calculo-de-frete-na-pagina-do-produto #calculo_frete_produto_altura');if(!widthProduct){return}
document.querySelector('#woocommerce-correios-calculo-de-frete-na-pagina-do-produto').style.display='block'}
function getDimension(){let dimensions={'heigth':document.querySelector('#woocommerce-correios-calculo-de-frete-na-pagina-do-produto #calculo_frete_produto_altura').value,'width':document.querySelector('#woocommerce-correios-calculo-de-frete-na-pagina-do-produto #calculo_frete_produto_largura').value,'length':document.querySelector('#woocommerce-correios-calculo-de-frete-na-pagina-do-produto #calculo_frete_produto_comprimento').value,'weight':document.querySelector('#woocommerce-correios-calculo-de-frete-na-pagina-do-produto #calculo_frete_produto_peso').value}
return dimensions}
function numberMask(content){return content.replace(/[^0-9-]+/g,"")}
function postalCodeMask(content,input){let value=content;let formatedValue='';if(isNaN(content)){input.value=numberMask(content);return}
formatedValue=value.replace(/(\d{5})(\d{1,2})$/,"$1-$2");input.value=formatedValue}
function usePostalCodeMask(evt=''){const inputDefault=evt?evt.target:evt;const inputShortcode=document.querySelector('.iptCepShortcode');if(inputDefault){const content=inputDefault.value;postalCodeMask(content,inputDefault)}
if(inputShortcode){const content=inputShortcode.value;postalCodeMask(content,inputShortcode)}}
function validateNumber(event){var key=window.event?event.keyCode:event.which;if(event.keyCode===8||event.keyCode===46){return!0}else if(key<48||key>57){return!1}else{return!0}}
;