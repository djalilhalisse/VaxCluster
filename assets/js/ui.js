(function(){
const L=window.I18N||{},loc=L.locale||'en-US',ws=L.weekStart||0,P=n=>String(n).padStart(2,'0');
const SV={chev:'<svg class="i chev" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>',cal:'<svg class="i" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',clock:'<svg class="i" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',l:'<svg class="i" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>',r:'<svg class="i" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>',ck:'<svg class="i" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>'};
const el=(t,c,h)=>{const e=document.createElement(t);if(c)e.className=c;if(h!==undefined)e.innerHTML=h;return e};
const closeAll=ex=>document.querySelectorAll('.ui-w.open').forEach(w=>{if(w!==ex){w.classList.remove('open');w.querySelector('.ui-btn').setAttribute('aria-expanded','false')}});
document.addEventListener('click',e=>{if(!e.composedPath().some(n=>n.classList&&n.classList.contains('ui-w')))closeAll()});
function build(native,icon,popup){
  const w=el('div','ui-w'),b=el('button','ui-btn'),txt=el('span'),pop=el('div','ui-pop');
  native.parentNode.insertBefore(w,native);w.appendChild(native);native.classList.add('ui-native');native.tabIndex=-1;
  b.type='button';b.setAttribute('aria-haspopup',popup);b.setAttribute('aria-expanded','false');b.append(txt);b.insertAdjacentHTML('beforeend',icon);
  const lab=native.id&&document.querySelector('label[for="'+native.id+'"]');if(lab){lab.id=lab.id||native.id+'-l';b.setAttribute('aria-labelledby',lab.id);lab.addEventListener('click',e=>{e.preventDefault();b.focus()})}
  native.addEventListener('invalid',()=>b.classList.add('bad'));native.addEventListener('change',()=>b.classList.remove('bad'));
  w.append(b,pop);
  const api={w,b,txt,pop,open(r){closeAll(w);w.classList.remove('up');r();w.classList.add('open');b.setAttribute('aria-expanded','true');const q=pop.getBoundingClientRect();if(q.bottom>innerHeight&&b.getBoundingClientRect().top>q.height+16)w.classList.add('up')},
    close(f){w.classList.remove('open');b.setAttribute('aria-expanded','false');if(f)b.focus()},isOpen:()=>w.classList.contains('open'),
    fire(){native.dispatchEvent(new Event('input',{bubbles:true}));native.dispatchEvent(new Event('change',{bubbles:true}))}};
  w.addEventListener('keydown',e=>{if(e.key==='Escape'&&api.isOpen()){e.stopPropagation();api.close(true)}});
  return api}
/* ---------- select ---------- */
function selectUI(s){const u=build(s,SV.chev,'listbox');u.pop.setAttribute('role','listbox');
  const sync=()=>{const o=s.options[s.selectedIndex];u.txt.textContent=o?o.textContent:'';u.b.classList.toggle('ph',!s.value)};
  const items=()=>[...u.pop.querySelectorAll('.ui-o')];
  function render(){u.pop.innerHTML='';[...s.options].forEach((o,i)=>{if(!o.value)return;const d=el('div','ui-o',''),sp=el('span');sp.textContent=o.textContent;d.append(sp);d.tabIndex=-1;d.setAttribute('role','option');
    const on=i===s.selectedIndex;d.setAttribute('aria-selected',on);if(on)d.insertAdjacentHTML('beforeend',SV.ck);
    d.onclick=()=>{s.selectedIndex=i;sync();u.fire();u.close(true)};d.dataset.i=i;u.pop.append(d)})}
  const openAt=()=>{u.open(render);const it=items();(it.find(x=>x.getAttribute('aria-selected')==='true')||it[0]).focus()};
  u.b.onclick=()=>u.isOpen()?u.close():openAt();
  u.b.addEventListener('keydown',e=>{if(['ArrowDown','ArrowUp','Enter',' '].includes(e.key)){e.preventDefault();openAt()}});
  u.pop.addEventListener('keydown',e=>{const it=items(),i=it.indexOf(document.activeElement);
    if(e.key==='ArrowDown'){e.preventDefault();it[Math.min(i+1,it.length-1)].focus()}else if(e.key==='ArrowUp'){e.preventDefault();it[Math.max(i-1,0)].focus()}
    else if(e.key==='Home'){e.preventDefault();it[0].focus()}else if(e.key==='End'){e.preventDefault();it[it.length-1].focus()}
    else if(e.key==='Enter'||e.key===' '){e.preventDefault();document.activeElement.click()}else if(e.key==='Tab')u.close()});
  s.addEventListener('change',sync);sync()}
/* ---------- date ---------- */
function dateUI(inp){const u=build(inp,SV.cal,'dialog');u.pop.classList.add('cal');u.pop.setAttribute('role','dialog');
  const parse=v=>{const m=/^(\d{4})-(\d\d)-(\d\d)$/.exec(v);return m?new Date(+m[1],m[2]-1,+m[3]):null},iso=d=>d.getFullYear()+'-'+P(d.getMonth()+1)+'-'+P(d.getDate());
  const fmt=new Intl.DateTimeFormat(loc,{day:'numeric',month:'long',year:'numeric'}),mfmt=new Intl.DateTimeFormat(loc,{month:'long',year:'numeric'}),mon=new Intl.DateTimeFormat(loc,{month:'short'});
  const min=parse(inp.min),max=parse(inp.max),today=new Date(),ti=iso(today);let view,mode;
  const sync=()=>{const d=parse(inp.value);u.txt.textContent=d?fmt.format(d):(L.pick_date||'');u.b.classList.toggle('ph',!d)};
  const out=d=>(min&&d<min)||(max&&d>max);
  function pick(d){inp.value=iso(d);sync();u.fire();u.close(true)}
  function render(){const p=u.pop;p.innerHTML='';const y=view.getFullYear(),m=view.getMonth();
    const nav=(dir,fn)=>{const b=el('button','cal-n',dir<0?SV.l:SV.r);b.type='button';b.onclick=fn;return b};
    const h=el('div','cal-h');const t=el('button','t');t.type='button';
    if(mode==='days'){t.textContent=mfmt.format(view);t.onclick=()=>{mode='months';render()};h.append(nav(-1,()=>{view=new Date(y,m-1,1);render()}),t,nav(1,()=>{view=new Date(y,m+1,1);render()}))}
    else{t.textContent=y;t.onclick=()=>{mode='days';render()};h.append(nav(-1,()=>{view=new Date(y-1,m,1);render()}),t,nav(1,()=>{view=new Date(y+1,m,1);render()}))}
    p.append(h);const g=el('div','cal-g'+(mode==='months'?' m':''));
    if(mode==='days'){for(let i=0;i<7;i++){const d=new Date(2024,0,7+((i+ws)%7));g.append(el('div','cal-wd',new Intl.DateTimeFormat(loc,{weekday:'narrow'}).format(d)))}
      const lead=(new Date(y,m,1).getDay()-ws+7)%7;for(let i=0;i<lead;i++)g.append(el('span'));
      const n=new Date(y,m+1,0).getDate(),sel=inp.value;for(let d=1;d<=n;d++){const dt=new Date(y,m,d),b=el('button','',d);b.type='button';const s=iso(dt);
        if(s===ti)b.classList.add('today');if(s===sel){b.classList.add('sel');b.setAttribute('aria-pressed','true')}b.disabled=out(dt);b.setAttribute('aria-label',fmt.format(dt));b.onclick=()=>pick(dt);g.append(b)}}
    else for(let i=0;i<12;i++){const b=el('button','');b.type='button';b.textContent=mon.format(new Date(y,i,1));if(i===m&&view.getFullYear()===y)b.classList.add('sel');b.onclick=()=>{view=new Date(y,i,1);mode='days';render()};g.append(b)}
    p.append(g);const f=el('div','cal-f'),c=el('button','',L.clear||'Clear'),td=el('button','',L.today||'Today');c.type=td.type='button';if(inp.required)c.style.visibility='hidden';
    td.disabled=out(new Date(today.getFullYear(),today.getMonth(),today.getDate()));c.onclick=()=>{inp.value='';sync();u.fire();u.close(true)};td.onclick=()=>pick(new Date());f.append(c,td);p.append(f)}
  const openIt=()=>u.open(()=>{let b=parse(inp.value)||today;if(max&&b>max)b=max;if(min&&b<min)b=min;view=new Date(b.getFullYear(),b.getMonth(),1);mode='days';render()});
  u.b.onclick=()=>u.isOpen()?u.close():(openIt(),(u.pop.querySelector('.sel,.today,.cal-g button:not(:disabled)')||{focus(){}}).focus());
  u.b.addEventListener('keydown',e=>{if(e.key==='ArrowDown'||e.key==='Enter'){e.preventDefault();openIt()}});sync()}
/* ---------- time ---------- */
function timeUI(inp){const u=build(inp,SV.clock,'dialog');u.pop.classList.add('tp');u.pop.setAttribute('role','dialog');let H=null,M=null;
  const read=()=>{const m=/^(\d\d):(\d\d)/.exec(inp.value);H=m?+m[1]:null;M=m?+m[2]:null};
  const sync=()=>{u.txt.textContent=inp.value?inp.value.slice(0,5):(L.pick_time||'');u.b.classList.toggle('ph',!inp.value)};
  function commit(){if(H===null)H=0;if(M===null)M=0;inp.value=P(H)+':'+P(M);sync();u.fire()}
  function render(){read();const p=u.pop;p.innerHTML='';const row=el('div','tp-c'),mk=(lab,n,cur,set)=>{const c=el('div'),s=el('small',null,lab),l=el('div','l');for(let i=0;i<n;i++){const b=el('button',i===cur?'sel':'',P(i));b.type='button';b.onclick=()=>{set(i);commit();render();center(p)};l.append(b)}c.append(s,l);return c};
    row.append(mk(L.hour||'Hour',24,H,v=>H=v),mk(L.minute||'Min',60,M,v=>M=v));p.append(row);
    const f=el('div','cal-f'),n=el('button','',L.now||'Now'),d=el('button','',L.done||'Done');n.type=d.type='button';n.onclick=()=>{const t=new Date();H=t.getHours();M=t.getMinutes();commit();render();center(p)};d.onclick=()=>u.close(true);f.append(n,d);p.append(f)}
  function center(p){p.querySelectorAll('.tp-c .l').forEach(c=>{const s=c.querySelector('.sel');if(s)c.scrollTop=s.offsetTop-c.clientHeight/2+s.offsetHeight/2})}
  u.b.onclick=()=>{if(u.isOpen())return u.close();u.open(render);center(u.pop)};
  u.b.addEventListener('keydown',e=>{if(e.key==='ArrowDown'||e.key==='Enter'){e.preventDefault();u.open(render);center(u.pop)}});sync()}
document.querySelectorAll('select.ui').forEach(selectUI);
document.querySelectorAll('input.ui[type=date]').forEach(dateUI);
document.querySelectorAll('input.ui[type=time]').forEach(timeUI);
/* ---------- repeatable rows (+ adds, x removes) ---------- */
document.querySelectorAll('.multi').forEach(m=>{const tpl=m.firstElementChild.cloneNode(true),rm=m.dataset.rm||'Remove',MAX=12;tpl.querySelector('input').removeAttribute('id');
  function wire(row){row.querySelector('.mpl').onclick=()=>{if(m.children.length>=MAX)return;const n=tpl.cloneNode(true),i=n.querySelector('input');i.value='';n.classList.add('x');
    const x=el('button','mrm','×');x.type='button';x.setAttribute('aria-label',rm);x.title=rm;x.onclick=()=>{const prev=n.previousElementSibling||n.nextElementSibling;n.remove();m.dispatchEvent(new Event('input',{bubbles:true}));if(prev)prev.querySelector('input').focus()};
    n.prepend(x);row.after(n);wire(n);i.focus()}}
  wire(m.firstElementChild)});
})();
