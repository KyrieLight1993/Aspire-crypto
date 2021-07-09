
// api url
const api_url = 
      "https://static.coinpaper.io/api/coins.json";
  
// Defining async function
async function getapi(url) {
    
    // Storing response
    const response = await fetch(url);
    
    // Storing data in form of JSON
    var data = await response.json();
    console.log(data);
    if (response) {
        hideloader();
    }
    show(data);
}
// Calling that async function
getapi(api_url);
function hideloader() {
    document.getElementById('loading').style.display = 'none';
}
// Function to define innerHTML for HTML table
function show(data) {
    let tab = 
        `<tr>
        <th>rank </th>
        <th>symbol</th>
          <th>marketcap</th>
          <th>name</th>
          <th>price</th>
          <th>%7d change</th>
          <th>%24h change</th>
         </tr>`;

    for (let r of data) {
        tab += `<tr> 
        <td>${r.rank}</td>          
        <td>${r.symbol}</td> 
    <td>${r.marketcap}</td>
    <td>${r.name}</td> 
    <td>${r.price}</td>  
    <td>${r.price_7d_percentage_change}</td>          
    <td>${r.price_24h_percentage_change}</td>          
</tr>`;
    }
    // Setting innerHTML as tab variable
    document.getElementById("crypto").innerHTML = tab;
}