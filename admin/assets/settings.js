const evcw_ai_provider = document.getElementById('evcw_ai_provider');
const evcw_ai_provider_local_url_wrap = document.querySelector('.evcw_ai_local_provider_url_wrap');
// console.log(evcw_ai_provider);

evcw_ai_provider.addEventListener('change', function(){
    this.value == 'local_model' ? evcw_ai_provider_local_url_wrap.classList.remove('hide') : evcw_ai_provider_local_url_wrap.classList.add('hide');
})