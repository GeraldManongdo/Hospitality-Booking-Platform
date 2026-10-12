(() => {
  const table=document.getElementById('admin-table');
  if(!table)return;
  const body=table.tBodies[0],search=document.getElementById('table-search'),filter=document.getElementById('status-filter'),caption=document.getElementById('page-caption'),count=document.getElementById('record-count'),empty=document.getElementById('no-records');
  const pageSize=window.AdminTableConfig?.pageSize||5;
  let page=0;
  const rows=()=>[...body.rows];
  [...new Set(rows().map((row)=>row.cells[row.cells.length-2]?.innerText.replace(/sample/ig,'').trim()).filter(Boolean))].forEach((status)=>{
    if(![...filter.options].some((option)=>option.value.toLowerCase()===status.toLowerCase())){
      const option=document.createElement('option');option.value=status;option.textContent=status;filter.append(option);
    }
  });
  const visible=()=>rows().filter((row)=>row.dataset.searchMatch!=='false');
  const update=()=>{
    const term=(search.value||'').trim().toLowerCase(),status=(filter.value||'').toLowerCase();
    rows().forEach((row)=>{const statusText=(row.cells[row.cells.length-2]?.innerText||'').toLowerCase();row.dataset.searchMatch=String(row.innerText.toLowerCase().includes(term)&&(!status||statusText.includes(status)));});
    const matches=visible(),pages=Math.max(1,Math.ceil(matches.length/pageSize));page=Math.min(page,pages-1);
    rows().forEach((row)=>{row.hidden=true;});
    matches.slice(page*pageSize,(page+1)*pageSize).forEach((row)=>{row.hidden=false;});
    count.textContent=String(matches.length);caption.textContent=matches.length?`Page ${page+1} of ${pages}`:'';
    empty.classList.toggle('d-none',matches.length>0);
    document.getElementById('prev-page').disabled=page===0;
    document.getElementById('next-page').disabled=page>=pages-1;
  };
  search.addEventListener('input',()=>{page=0;update();});
  filter.addEventListener('change',()=>{page=0;update();});
  document.getElementById('prev-page').addEventListener('click',()=>{page=Math.max(0,page-1);update();});
  document.getElementById('next-page').addEventListener('click',()=>{page+=1;update();});
  body.addEventListener('click',(event)=>{
    const button=event.target.closest('.row-action');if(!button)return;
    const cell=button.closest('tr').cells[button.closest('tr').cells.length-2],current=cell.innerText.replace(/sample/ig,'').trim();
    const cycle=['Review','Active','Pending','Published','Draft','Open','In progress','Confirmed','Cancelled','Completed','Available','Unavailable','Flagged','Scheduled','Ended','Closed','Ready'];
    const next=cycle[(cycle.indexOf(current)+1+cycle.length)%cycle.length];
    cell.innerHTML=`${next} <span class="status-pill status-review ms-1">sample</span>`;
    const note=document.getElementById('admin-feedback');note.textContent=`Sample display status changed to “${next}” on this page only.`;note.classList.remove('d-none');update();
  });
  const toggle=document.getElementById('toggle-editor');
  toggle?.addEventListener('click',()=>{const editor=document.getElementById('record-editor'),open=editor.classList.toggle('d-none')===false;toggle.setAttribute('aria-expanded',String(open));if(open)editor.scrollIntoView({behavior:'smooth',block:'center'});});
  document.getElementById('record-form')?.addEventListener('submit',(event)=>{
    event.preventDefault();event.stopPropagation();const form=event.currentTarget;form.classList.add('was-validated');if(!form.checkValidity())return;
    const config=window.AdminTableConfig,values=[document.getElementById('record-ref').value,...config.columns.slice(1,3).map((_,index)=>document.getElementById(`record-field-${index}`).value),document.getElementById('record-status').value];
    while(values.length<config.columns.length)values.splice(values.length-1,0,'Sample');
    const row=body.insertRow();values.forEach((value)=>{const cell=row.insertCell();cell.textContent=value;});const action=row.insertCell();action.innerHTML='<button class="btn btn-sm btn-outline-primary row-action" type="button">Cycle status</button>';
    form.reset();form.classList.remove('was-validated');document.getElementById('record-editor').classList.add('d-none');toggle.setAttribute('aria-expanded','false');page=0;update();
    const note=document.getElementById('admin-feedback');note.textContent='Sample record added to this page only; it has not been saved.';note.classList.remove('d-none');
  });
  update();
})();
