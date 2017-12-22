

function addopenapi(){
    var el   = document.createElement("script");
    el.type  = "text/javascript";
    el.src   = "//vk.com/js/api/openapi.js";
    el.async = true;
    document.head.appendChild(el);
};

window.vkAsyncInit = function() {VK.init({ apiId: 5983853});};

window.addEventListener('load',function(){
    
addopenapi();

  })



