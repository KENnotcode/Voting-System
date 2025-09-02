const XLSX = require('xlsx');
const fs = require('fs');
const path = require('path');

try {
  // Read the Excel file
  const workbook = XLSX.readFile('JSON Templates/candidates.xlsx');
  
  // Get the first sheet name
  const sheetName = workbook.SheetNames[0];
  const worksheet = workbook.Sheets[sheetName];
  
  // Convert the sheet to JSON
  const jsonData = XLSX.utils.sheet_to_json(worksheet);
  
  // Write the JSON data to a file
  fs.writeFileSync('JSON Templates/candidates_converted.json', JSON.stringify(jsonData, null, 2));
  
  console.log('Successfully converted Excel to JSON');
  console.log('Data converted:', jsonData.length, 'records');
} catch (err) {
  console.error('Error converting Excel to JSON:', err);
}
