function elm(e){return document.querySelector(e)}function ajax(e,t,n,o){if(void 0===e)return null;var i=new FormData;for(var r in t)t.hasOwnProperty(r)&&i.append(r,t[r]);var a=new XMLHttpRequest;a.open("POST",e,!0),void 0!==n&&"function"==typeof n&&a.addEventListener("load",function(e){n(e.target.response)}),void 0!==o&&"function"==typeof o&&a.upload.addEventListener("progress",function(e){e.lengthComputable&&o(e)}),a.send(void 0===i?null:i)}function isset(e){return void 0!==e}function upload(e){ajax("/admin/media",{file:e[0],_token:elm("[name=_token]").value,_method:elm("[name=_method]").value},function(e){e=JSON.parse(e),isset(e.error)?window.console.log(e.error):elm("#drop-container").style.backgroundImage="url('"+e.folder+"')"},function(e){var t=Math.floor(e.loaded/e.total*100);0===info&&(info++,elm(".info-text").textContent="Uploading"),t>=100&&(elm(".info-text").textContent="Finished"),elm("[data-percent]").setAttribute("data-percent",t),elm("tspan").textContent=t+"%"})}HTMLElement.prototype.onDrop=function(e){return this.addEventListener("drop",function(t){if(t.stopPropagation(),t.preventDefault(),"function"==typeof e){e.bind(this)(t.dataTransfer.files)}},!1),this},HTMLElement.prototype.onDragOver=function(e){return this.addEventListener("dragover",function(t){if(t.stopPropagation(),t.preventDefault(),"function"==typeof e){e.bind(this)(t)}},!1),this},HTMLElement.prototype.onDragLeave=function(e){return this.addEventListener("dragleave",function(t){if(t.stopPropagation(),t.preventDefault(),"function"==typeof e){e.bind(this)(t)}},!1),this};var info=0;elm("#drop-container").onDrop(function(e){e.length>0&&upload(e)}).onDragOver(function(){this.className="drop active"}).onDragLeave(function(){this.className="drop"}),elm("#file").addEventListener("change",function(){this.files.length>0&&upload(this.files)});