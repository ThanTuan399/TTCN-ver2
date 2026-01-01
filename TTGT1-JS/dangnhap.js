const login = document.querySelector('.loginn')
const dangnhap = document.querySelector('.ttindangnhap')
const dangky = document.querySelector('.ttindangky')
const register = document.querySelector('.register')
const forgetpw = document.querySelector('.forgetpw')
const huy = document.querySelector('.huy')
const getpw = document.querySelector('.getpw')
const bongmo = document.querySelector('.bongmo')
register.addEventListener('click', function(){
    register.style.color='#6d6459'
    register.style.borderBottom='2px solid #6d6459'
    dangky.style.display='flex'
    dangnhap.style.display='none'
    login.style.color='black'
    login.style.borderBottom='2px solid #F0F0F0'
})
login.addEventListener('click', function(){
    login.style.color='#6d6459'
    login.style.borderBottom='2px solid #6d6459'
    dangnhap.style.display='flex'
    dangky.style.display='none'
    register.style.color='black'
    register.style.borderBottom='2px solid #F0F0F0'
})
huy.addEventListener('click', function(){
    console.log('clicked huy')
    getpw.classList.remove('active')
    bongmo.classList.remove('active')
    document.body.classList.remove('modal-open')
    console.log('clicked huy2')
})
forgetpw.addEventListener('click', function(){
    getpw.classList.add('active')
    bongmo.classList.add('active')
    document.body.classList.add('modal-open')
})
