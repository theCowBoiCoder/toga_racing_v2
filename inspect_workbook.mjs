import fs from "node:fs/promises";
import { FileBlob, SpreadsheetFile } from "@oai/artifact-tool";

const source = "F:/Downloads/2026 24h Spa - Oversteer 13_00.xlsx";
const outDir = "F:/Documents/toga_racing_v2/outputs/019fc7cb-3fa2-7de0-ad6a-5bc5ff829aef/source_previews";
await fs.mkdir(outDir, { recursive: true });
const workbook = await SpreadsheetFile.importXlsx(await FileBlob.load(source));
const summary = await workbook.inspect({
  kind: "workbook,sheet,table,formula,drawing",
  maxChars: 16000,
  tableMaxRows: 20,
  tableMaxCols: 20,
  options: { maxResults: 200 },
});
console.log(summary.ndjson);
for (const sheet of workbook.worksheets.items) {
  const used = sheet.getUsedRange();
  console.log(`SHEET ${sheet.name} USED ${used?.address ?? "none"}`);
  if (used) {
    const details = await workbook.inspect({ kind: "table", sheetId: sheet.name, range: used.address, include: "values,formulas", tableMaxRows: 60, tableMaxCols: 30, maxChars: 20000 });
    console.log(details.ndjson);
  }
  const preview = await workbook.render({ sheetName: sheet.name, autoCrop: "all", scale: 1, format: "png" });
  await fs.writeFile(`${outDir}/${sheet.name.replace(/[^a-z0-9_-]/gi, "_")}.png`, new Uint8Array(await preview.arrayBuffer()));
}
