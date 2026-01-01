const bars = document.querySelector('.bars')
const bongmo = document.querySelector('.bongmo')
const tat = document.querySelector('.tat')
const nhap = document.querySelector('.doimk')
tat.addEventListener('click', function(){
    bars.style.display='none'
    bongmo.style.display='none'
    document.body.classList.remove('modal-open');
})
nhap.addEventListener('click', function(){
    bars.style.display='block'
    bongmo.style.display='block'
    document.body.classList.add('modal-open');
})