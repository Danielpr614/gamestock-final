// A validação do navegador orienta; o PHP valida novamente todos os dados.
const pendingForms=new Map();
const restoreForms=()=>{pendingForms.forEach((buttons,form)=>{delete form.dataset.sending;buttons.forEach(button=>button.disabled=false);});pendingForms.clear();};
document.querySelectorAll('form').forEach(form=>{
  const password=form.querySelector('[name="senha"]'),confirmation=form.querySelector('[name="confirmacao"]');
  const validate=()=>{if(confirmation)confirmation.setCustomValidity(confirmation.value!==password.value?'As senhas precisam ser iguais.':'');if(password){const bytes=new TextEncoder().encode(password.value).length;password.setCustomValidity(bytes>72?'A senha ultrapassa 72 bytes.':'');}};
  if(password)password.addEventListener('input',validate);if(confirmation)confirmation.addEventListener('input',validate);
  form.addEventListener('submit',event=>{
    validate();if(!form.reportValidity()){event.preventDefault();return;}
    if(form.dataset.confirm&&!window.confirm(form.dataset.confirm)){event.preventDefault();return;}
    // Consultas GET podem apenas mudar o fragmento da URL, sem recarregar.
    // Elas precisam continuar disponíveis mesmo ao repetir o mesmo filtro.
    if(event.defaultPrevented||form.method.toLowerCase()!=='post')return;
    if(form.dataset.sending){event.preventDefault();return;}form.dataset.sending='1';
    const buttons=[...form.querySelectorAll('button')].filter(button=>!button.disabled);
    pendingForms.set(form,buttons);
    // Campos e tokens continuam sendo enviados; preserva botões já indisponíveis.
    requestAnimationFrame(()=>{if(pendingForms.has(form))buttons.forEach(button=>button.disabled=true);});
  });
});
window.addEventListener('pageshow',restoreForms);
window.addEventListener('pagehide',restoreForms);

// Destaques: 7 segundos por jogo. Movimento reduzido altera só as transições CSS.
// A troca continua disponível; pausa manual, hover e foco suspendem o timer.
document.querySelectorAll('[data-featured]').forEach(feature=>{
  const slides=[...feature.querySelectorAll('[data-featured-slide]')];
  const buttons=[...feature.querySelectorAll('[data-featured-target]')];
  const controls=feature.querySelector('.featured-controls');
  const pause=feature.querySelector('[data-featured-pause]');
  const status=feature.querySelector('[data-featured-status]');
  if(!controls||!buttons.length||!pause)return;
  let active=0,timer=null,paused=false,hovering=false,keyboardFocus=false;
  const restart=()=>{
    clearTimeout(timer);timer=null;
    pause.disabled=false;
    const label=paused?'Retomar destaques':'Pausar destaques';
    pause.setAttribute('aria-label',label);pause.title=label;
    pause.querySelector('[data-icon-pause]').hidden=paused;
    pause.querySelector('[data-icon-play]').hidden=!paused;
    const interacting=hovering||(keyboardFocus&&feature.contains(document.activeElement));
    feature.dataset.autoplay=paused?'paused':document.hidden?'background':interacting?'interaction':'playing';
    if(feature.dataset.autoplay==='playing')timer=setTimeout(()=>{select((active+1)%slides.length);},7000);
  };
  const select=(index,focus=false,announce=false)=>{
    active=index;
    slides.forEach((slide,i)=>{slide.hidden=i!==index;});
    buttons.forEach((button,i)=>button.setAttribute('aria-pressed',String(i===index)));
    if(focus)buttons[index].focus();
    if(announce&&status)status.textContent='Destaque '+(index+1)+' de '+slides.length+': '+slides[index].getAttribute('aria-label');
    restart();
  };
  controls.hidden=false;
  buttons.forEach((button,i)=>{
    button.addEventListener('click',()=>select(i,false,true));
    button.addEventListener('keydown',event=>{
      let next=i;
      if(event.key==='ArrowRight')next=(i+1)%buttons.length;
      else if(event.key==='ArrowLeft')next=(i-1+buttons.length)%buttons.length;
      else if(event.key==='Home')next=0;
      else if(event.key==='End')next=buttons.length-1;
      else return;
      event.preventDefault();select(next,true,true);
    });
  });
  pause.addEventListener('click',()=>{paused=!paused;restart();});
  feature.addEventListener('mouseenter',()=>{hovering=true;restart();});
  feature.addEventListener('mouseleave',()=>{hovering=false;restart();});
  feature.addEventListener('pointerdown',()=>{keyboardFocus=false;restart();});
  feature.addEventListener('keydown',()=>{keyboardFocus=true;restart();});
  feature.addEventListener('focusin',event=>{keyboardFocus=event.target.matches(':focus-visible');restart();});
  feature.addEventListener('focusout',()=>setTimeout(()=>{if(!feature.contains(document.activeElement))keyboardFocus=false;restart();},0));
  document.addEventListener('visibilitychange',restart);
  window.addEventListener('pagehide',()=>{clearTimeout(timer);timer=null;});
  window.addEventListener('pageshow',restart);
  restart();
});
// A confirmação aparece somente após o servidor aceitar a adição ao carrinho.
if(document.querySelector('[data-cart-feedback]')){
  const counter=document.querySelector('.counter');
  if(counter)counter.classList.add('cart-updated');
}
