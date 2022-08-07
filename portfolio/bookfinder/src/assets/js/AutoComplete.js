import FetchSubjects from "./FetchSubjects.js";
import {setDelay} from "./Helpers.js";

export default async function AutoComplete(inp)
{
    let a = null;
    let getListDelayed = setDelay(getList, 500);
    inp.addEventListener("input", getListDelayed);
    inp.addEventListener("click", getList);
    
    async function getList(e)
    {
        let b = null;
        let val = this.value;
        closeList(e);
        if (!val.trim()) return false;
        if (a) return false;
        a = document.createElement("ul");
        a.setAttribute("class", "autocomplete-items");
        this.parentNode.append(a);

        let arr = await FetchSubjects(inp);

        for (let i = 0; i < arr.length; i++) {
            b = document.createElement("li");
            b.textContent = arr[i].name + " " + arr[i].work_count + " books";
            b.dataset.key = arr[i].key.slice(10);
            b.addEventListener("click", function(e) {
                inp.value = this.dataset.key;
                closeList(e);
                document.getElementById("bookSearch").click();
            });
            a.append(b);
        }
        document.addEventListener("click", closeList);
    
        function closeList(e) {
        if (e.target==inp && e.type=="click") return;
        if (a) a.remove();
        a=null;
        document.removeEventListener("click", closeList);
        }
    }

}