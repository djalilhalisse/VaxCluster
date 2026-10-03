(function(){const c=document.getElementById('cluster');if(!c)return;const x=c.getContext('2d'),dpr=Math.min(devicePixelRatio||1,2);
const cols=['#5b8cff','#ffb020','#2dd4a3'],ctr=[[.32,.35],[.7,.42],[.48,.72]],pts=[];let W,H;
function size(){const r=c.getBoundingClientRect();W=r.width;H=r.height;c.width=W*dpr;c.height=H*dpr;x.setTransform(dpr,0,0,dpr,0,0)}size();addEventListener('resize',size);
ctr.forEach((m,k)=>{for(let i=0;i<46;i++){const a=Math.random()*6.28,d=Math.pow(Math.random(),.6)*.15;pts.push({k,bx:m[0]+Math.cos(a)*d,by:m[1]+Math.sin(a)*d,p:Math.random()*6.28})}});
const still=matchMedia('(prefers-reduced-motion:reduce)').matches;let t=0;
function draw(){x.clearRect(0,0,W,H);ctr.forEach((m,k)=>{x.beginPath();x.arc(m[0]*W,m[1]*H,.2*W,0,6.28);x.fillStyle=cols[k]+'22';x.fill()});
pts.forEach(p=>{const px=(p.bx+Math.sin(t+p.p)*.008)*W,py=(p.by+Math.cos(t*.8+p.p)*.008)*H;x.beginPath();x.arc(px,py,3.2,0,6.28);x.fillStyle=cols[p.k];x.globalAlpha=.85;x.fill();x.globalAlpha=1});
// new patient: travels the loop, links to nearest cluster
const q=(t*.12)%3,a=ctr[Math.floor(q)],b=ctr[(Math.floor(q)+1)%3],f=q%1,e=f<.5?2*f*f:1-Math.pow(-2*f+2,2)/2,nx=(a[0]+(b[0]-a[0])*e)*W,ny=(a[1]+(b[1]-a[1])*e)*H-Math.sin(e*3.14)*.1*H;
let bi=0,bd=9e9;ctr.forEach((m,k)=>{const d=Math.hypot(m[0]*W-nx,m[1]*H-ny);if(d<bd){bd=d;bi=k}});
x.beginPath();x.moveTo(nx,ny);x.lineTo(ctr[bi][0]*W,ctr[bi][1]*H);x.strokeStyle=cols[bi];x.setLineDash([5,5]);x.lineWidth=1.5;x.stroke();x.setLineDash([]);
ctr.forEach((m,k)=>{x.beginPath();x.arc(m[0]*W,m[1]*H,9,0,6.28);x.strokeStyle=cols[k];x.lineWidth=2.5;x.stroke()});
x.beginPath();x.arc(nx,ny,9,0,6.28);x.fillStyle='#fff';x.fill();x.strokeStyle='#5b8cff';x.lineWidth=3;x.stroke();
if(!still){t+=.016;requestAnimationFrame(draw)}}draw()})();
