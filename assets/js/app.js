(function(){const q=document.getElementById('q'),v=document.getElementById('vf');if(!q)return;
const rows=[...document.querySelectorAll('#pt tbody tr[data-s]')],n=document.getElementById('count'),e=document.getElementById('none');
function f(){const s=q.value.trim().toLowerCase(),vv=v.value;let c=0;rows.forEach(r=>{const ok=r.dataset.s.includes(s)&&(!vv||r.dataset.v===vv);r.hidden=!ok;if(ok)c++});n.textContent=c;e.hidden=c>0}
q.addEventListener('input',f);v.addEventListener('change',f)})();
