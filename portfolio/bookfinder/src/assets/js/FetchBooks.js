export default async function FetchBooks (subject, forPage, offset)
{
  let response = await fetch(`https://openlibrary.org/subjects/${subject}.json?limit=${forPage}&offset=${offset}`);
    if (response.ok) {
      let json = await response.json();
      if(json.work_count>0) {
        return json;
        } else {
        return false;
        }
    } else {
      alert("HTTP-Error: " + response.status);
    }
}