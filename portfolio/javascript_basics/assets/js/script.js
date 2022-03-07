buttons=document.querySelector('#buttons');
let button_values = ['+','reset','-'];

//Buttons creation
function getButtons() {
    let buttons_array = [];
    for(let button_value of button_values) {
      let newButton = document.createElement('button');
      newButton.dataset.operation = button_value;
      newButton.textContent = button_value.toString();
      buttons_array.push(newButton);
    }
    return buttons_array;
} 
buttons.append(...getButtons());

//Display creation
let display = document.createElement('div');
display.setAttribute('id', 'display');
display.textContent = '0';
displayContainer=document.querySelector('.counter-display');
displayContainer.firstElementChild.after(display);

//Counter funcionalities
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