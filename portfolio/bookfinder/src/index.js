import AutoComplete from "./assets/js/AutoComplete.js";
import GetBookList from "./assets/js/GetBookList.js";
import "./style.scss";

let bookContainer = document.querySelector('.bookContainer');
let autoComplete = document.querySelector('.autoComplete');
let bookForm = document.forms.bookInputs;
let subjectInput = bookForm.elements.subject;

AutoComplete(subjectInput);
GetBookList(bookForm, bookContainer);