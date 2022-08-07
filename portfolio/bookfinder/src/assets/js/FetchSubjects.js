export default async function FetchSubjects(inp)
{
    let subject = inp.value;
    let response = await fetch(`https://openlibrary.org/search/subjects.json?q=${subject}* OR ${subject}`);
    if (response.ok) {
        let json = await response.json();
        if(json.numFound>0) {
            let suggestionList = json.docs;
            return suggestionList;
        } else {
            return false;
        }
    } else {
        alert("HTTP-Error: " + response.status);
    }
}