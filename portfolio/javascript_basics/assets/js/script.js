buttons=document.querySelector('#buttons');
display=document.querySelector('#display');
      
buttons.addEventListener('click', Calculation);
  
function Calculation (event) {
    let operation = event.target.dataset.operation;
    let value = +display.textContent;
    if (!operation)
    return
    switch(operation) {
        case '+':
            value++;
            break;
        case '-':
            value--;
            break;
        case 'reset':
            value = 0;
            break;
        default:
            return
    }
    updateValue(value);
}

function updateValue(number) {
    display.textContent = number.toString();
}
