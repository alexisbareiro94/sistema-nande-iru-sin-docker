import axios from "axios";
import { $, $$, $el } from './utils';
if(window.location.pathname == '/inventario'){
const div = $('#div-cerrar');
const drop = $('#export-menu-prod');


    $el('#dropdown-prod', 'click', () => {
        div.classList.remove('hidden');    
        const icon = $('#icon-flecha-prod'); 
        icon.classList.toggle('rotate-180');
        drop.classList.toggle('hidden');
    })
    
    $el('#div-cerrar', 'click', () => {    
        $('#div-cerrar').classList.add('hidden');    
        const icon = $('#icon-flecha-prod'); 
        icon.classList.toggle('rotate-180');
        drop.classList.toggle('hidden');
    })
}