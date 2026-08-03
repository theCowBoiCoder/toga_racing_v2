import fs from "node:fs/promises";
import { SpreadsheetFile, Workbook } from "@oai/artifact-tool";

const outputDir = "F:/Documents/toga_racing_v2/outputs/019fc7cb-3fa2-7de0-ad6a-5bc5ff829aef";
const outputPath = `${outputDir}/Toga_Racing_Stint_Planner_Driver_Selection.xlsx`;
await fs.mkdir(outputDir, { recursive: true });

const wb = Workbook.create();
const planner = wb.worksheets.add("Stint Planner");
const team = wb.worksheets.add("Team & Settings");
const cars = wb.worksheets.add("Car Data");
const guide = wb.worksheets.add("Quick Guide");

const navy = "#172033";
const cyan = "#25C2D8";
const blue = "#2563EB";
const paleBlue = "#E8F4FA";
const inputFill = "#FFF2CC";
const formulaFill = "#E2F0D9";
const light = "#F3F6FA";
const border = "#C7D0DA";
const textDark = "#182230";
const muted = "#5C6773";
const white = "#FFFFFF";
const red = "#FCE8E6";

for (const sh of [planner, team, cars, guide]) sh.showGridLines = false;

// ---------------- Main planner ----------------
planner.getRange("A1:J1").merge();
planner.getRange("A1").values = [["TOGA RACING · STINT PLANNER"]];
planner.getRange("A1:J1").format = { fill: navy, font: { bold: true, color: white, size: 18 }, verticalAlignment: "center" };
planner.getRange("A1:J1").format.rowHeight = 34;
planner.getRange("A2:J2").merge();
planner.getRange("A2").values = [["Yellow cells are inputs · Green cells calculate automatically · Times roll across midnight using the race date"]];
planner.getRange("A2:J2").format = { fill: "#DCE6F1", font: { color: textDark, italic: true }, verticalAlignment: "center" };
planner.getRange("A2:J2").format.rowHeight = 24;

planner.getRange("A4:B4").merge();
planner.getRange("A4").values = [["RACE INPUTS"]];
planner.getRange("A4:B4").format = { fill: cyan, font: { bold: true, color: navy } };
planner.getRange("D4:E4").merge();
planner.getRange("D4").values = [["PLAN SUMMARY"]];
planner.getRange("D4:E4").format = { fill: cyan, font: { bold: true, color: navy } };

const inputRows = [
  ["Event", "24h Spa"],
  ["Sim", "iRacing"],
  ["Car", "Ferrari 296 GT3"],
  ["Class", null],
  ["Sourced capacity / limit", null],
  ["Fuel unit", null],
  ["Usable tank override", 101],
  ["Track", "Spa-Francorchamps"],
  ["Race date", new Date(2026, 6, 11)],
  ["Start time (local)", 13.75 / 24],
  ["Race length (mins)", 1440],
  ["Average lap (secs)", 138],
  ["Fuel per lap", 4],
  ["Reserve laps", 2],
  ["Max stint (mins)", 56],
  ["Pit loss between stints (secs)", 60],
];
planner.getRange("A5:B20").values = inputRows;
planner.getRange("A5:A20").format = { fill: light, font: { bold: true, color: textDark } };
planner.getRange("B5:B20").format = { fill: inputFill, font: { color: textDark }, borders: { preset: "outside", style: "thin", color: border } };
planner.getRange("B8:B10").format.fill = formulaFill;
planner.getRange("B8").formulas = [["=IFERROR(INDEX('Car Data'!$C$2:$C$60,MATCH(B6&\"|\"&B7,'Car Data'!$G$2:$G$60,0)),\"\")"]];
planner.getRange("B9").formulas = [["=IFERROR(SUMIF('Car Data'!$G$2:$G$60,B6&\"|\"&B7,'Car Data'!$D$2:$D$60),\"\")"]];
planner.getRange("B10").formulas = [["=IFERROR(INDEX('Car Data'!$E$2:$E$60,MATCH(B6&\"|\"&B7,'Car Data'!$G$2:$G$60,0)),\"units\")"]];
planner.getRange("B6").dataValidation = { rule: { type: "list", values: ["LMU", "iRacing"] } };
planner.getRange("B7").dataValidation = { rule: { type: "list", formula1: "=IF($B$6=\"LMU\",'Car Data'!$B$2:$B$31,'Car Data'!$B$32:$B$49)" } };
planner.getRange("B11").dataValidation = { rule: { type: "decimal", operator: "between", formula1: 0, formula2: 1000 } };
planner.getRange("B15:B20").dataValidation = { rule: { type: "decimal", operator: "between", formula1: 0, formula2: 100000 } };
planner.getRange("B13").format.numberFormat = "dd mmm yyyy";
planner.getRange("B14").format.numberFormat = "hh:mm";
planner.getRange("B9:B11").format.numberFormat = "0.0";
planner.getRange("B15:B20").format.numberFormat = "0.0";

const summaryLabels = [
  ["Race starts"], ["Race finishes"], ["Planning capacity"], ["Estimated race laps"],
  ["Max racing laps / tank"], ["Fuel-limited stint (mins)"], ["Planned stint (mins)"],
  ["Estimated stints"], ["Estimated race fuel"], ["Reserve per stop"], ["Scheduled driving"], ["Scheduled pit time"],
];
planner.getRange("D5:D16").values = summaryLabels;
planner.getRange("D5:D16").format = { fill: light, font: { bold: true, color: textDark } };
planner.getRange("E5:E16").format = { fill: formulaFill, font: { color: textDark }, borders: { preset: "outside", style: "thin", color: border } };
planner.getRange("E5:E16").formulas = [
  ["=B13+B14"],
  ["=E5+B15/1440"],
  ["=IF(B11>0,B11,B9)"],
  ["=SUM(G25:G60)"],
  ["=IFERROR(MAX(0,ROUNDDOWN(E7/B17,0)-B18),0)"],
  ["=IFERROR(E9*B16/60,0)"],
  ["=IFERROR(MIN(B19,E10),0)"],
  ["=IFERROR(ROUNDUP((B15+B20/60)/(E11+B20/60),0),0)"],
  ["=E8*B17"],
  ["=B18*B17"],
  ["=SUM(F25:F60)"],
  ["=MAX(0,E12-1)*B20/60"],
];
planner.getRange("E5:E6").format.numberFormat = "ddd dd mmm yyyy hh:mm";
planner.getRange("E7:E16").format.numberFormat = "0.0";
planner.getRange("E8:E9").format.numberFormat = "0";
planner.getRange("E12:E13").format.numberFormat = "0";

planner.getRange("G4:J4").merge();
planner.getRange("G4").values = [["IMPORTANT"]];
planner.getRange("G4:J4").format = { fill: "#F4B183", font: { bold: true, color: navy } };
planner.getRange("G5:J10").merge();
planner.getRange("G5").values = [["Fuel use is track, weather, setup and driver dependent—enter your measured fuel per lap. LMU Hypercar/LMGT3 references use Spa 2026 maximum stint energy (MJ), while iRacing references use published tank litres where an official manual was found. Session rules and BoP may reduce usable fuel, so the override is the planning source of truth."]];
planner.getRange("G5:J10").format = { fill: "#FFF4E5", font: { color: textDark }, wrapText: true, verticalAlignment: "top", borders: { preset: "outside", style: "thin", color: "#E6B566" } };
planner.getRange("G5:J10").format.rowHeight = 20;

planner.getRange("G12:J12").merge();
planner.getRange("G12").values = [["Driver totals update from the stint table"]];
planner.getRange("G12:J12").format = { fill: cyan, font: { bold: true, color: navy } };
planner.getRange("G13:J13").values = [["Driver", "Stints", "Drive mins", "Est. laps"]];
planner.getRange("G13:J13").format = { fill: navy, font: { bold: true, color: white } };
for (let r = 14; r <= 21; r++) {
  const teamRow = r - 10;
  planner.getRange(`G${r}`).formulas = [[`=IF('Team & Settings'!E${teamRow}=\"\",\"\",'Team & Settings'!E${teamRow})`]];
  planner.getRange(`H${r}`).formulas = [[`=IF(G${r}=\"\",\"\",COUNTIF($B$25:$B$60,G${r}))`]];
  planner.getRange(`I${r}`).formulas = [[`=IF(G${r}=\"\",\"\",SUMIF($B$25:$B$60,G${r},$F$25:$F$60))`]];
  planner.getRange(`J${r}`).formulas = [[`=IF(G${r}=\"\",\"\",SUMIF($B$25:$B$60,G${r},$G$25:$G$60))`]];
}
planner.getRange("G14:J21").format = { fill: white, borders: { preset: "inside", style: "thin", color: border } };
planner.getRange("H14:H21").format.numberFormat = "0";
planner.getRange("I14:I21").format.numberFormat = "0.0";
planner.getRange("J14:J21").format.numberFormat = "0";

planner.getRange("A23:J23").merge();
planner.getRange("A23").values = [["STINT SCHEDULE"]];
planner.getRange("A23:J23").format = { fill: cyan, font: { bold: true, color: navy } };
planner.getRange("A24:J24").values = [["Stint", "Driver", "Stand-by", "Start", "End", "Drive mins", "Est. laps", "Fuel needed", "Start fuel target", "Notes"]];
planner.getRange("A24:J24").format = { fill: navy, font: { bold: true, color: white }, wrapText: true, verticalAlignment: "center" };
planner.getRange("A24:J24").format.rowHeight = 28;
for (let r = 25; r <= 60; r++) {
  planner.getRange(`A${r}`).values = [[r - 24]];
  planner.getRange(`B${r}:C${r}`).dataValidation = { rule: { type: "list", formula1: "='Team & Settings'!$E$4:$E$11" } };
  if (r === 25) planner.getRange(`D${r}`).formulas = [[`=IF($A${r}<=$E$12,$E$5,\"\")`]];
  else planner.getRange(`D${r}`).formulas = [[`=IF($A${r}<=$E$12,E${r-1}+$B$20/86400,\"\")`]];
  planner.getRange(`E${r}`).formulas = [[`=IF(D${r}=\"\",\"\",MIN($E$6,D${r}+F${r}/1440))`]];
  planner.getRange(`F${r}`).formulas = [[`=IF(D${r}=\"\",\"\",MIN($E$11,MAX(0,($E$6-D${r})*1440)))`]];
  planner.getRange(`G${r}`).formulas = [[`=IF(F${r}=\"\",\"\",ROUNDUP(F${r}*60/$B$16,0))`]];
  planner.getRange(`H${r}`).formulas = [[`=IF(G${r}=\"\",\"\",G${r}*$B$17)`]];
  planner.getRange(`I${r}`).formulas = [[`=IF(H${r}=\"\",\"\",MIN($E$7,H${r}+$B$18*$B$17))`]];
}
planner.getRange("A25:J60").format = { borders: { preset: "inside", style: "thin", color: border }, verticalAlignment: "center" };
planner.getRange("B25:C60").format.fill = inputFill;
planner.getRange("J25:J60").format.fill = inputFill;
planner.getRange("D25:I60").format.fill = formulaFill;
planner.getRange("D25:E60").format.numberFormat = "ddd dd mmm hh:mm";
planner.getRange("F25:F60").format.numberFormat = "0.0";
planner.getRange("G25:G60").format.numberFormat = "0";
planner.getRange("H25:I60").format.numberFormat = "0.0";
planner.getRange("A25:A60").format.font = { bold: true, color: navy };
planner.getRange("A25:A60").format.horizontalAlignment = "center";
planner.freezePanes.freezeRows(24);

planner.getRange("A1:J60").format.font.name = "Aptos";
planner.getRange("A1:A60").format.columnWidth = 24;
planner.getRange("B1:B60").format.columnWidth = 22;
planner.getRange("C1:C60").format.columnWidth = 17;
planner.getRange("D1:E60").format.columnWidth = 24;
planner.getRange("F1:I60").format.columnWidth = 15;
planner.getRange("G1:G60").format.columnWidth = 23;
planner.getRange("J1:J60").format.columnWidth = 28;

// ---------------- Team & Settings ----------------
team.getRange("A1:H1").merge();
team.getRange("A1").values = [["TEAM & OPTIONAL SETTINGS"]];
team.getRange("A1:H1").format = { fill: navy, font: { bold: true, color: white, size: 16 } };
team.getRange("A3:B3").values = [["#", "Master driver list"]];
team.getRange("A3:B3").format = { fill: cyan, font: { bold: true, color: navy } };
const masterDrivers = ["Hayden Sweet", "Stijn Donckerwolke", "Mitchell Sterrenberg", "Lukas James", "Konrad Wasowicz", "Lukas Küthe", "Troy-Fraser McGonigal", "Jordan McGonigal", "", "", "", "", "", "", "", "", "", "", "", ""];
team.getRange("A4:A23").values = masterDrivers.map((_, i) => [i + 1]);
team.getRange("B4:B23").values = masterDrivers.map((d) => [d]);
team.getRange("B4:B23").format.fill = inputFill;
team.getRange("A4:B23").format.borders = { preset: "inside", style: "thin", color: border };
team.getRange("D3:E3").values = [["Race slot", "Event roster"]];
team.getRange("D3:E3").format = { fill: cyan, font: { bold: true, color: navy } };
team.getRange("D4:D11").values = Array.from({ length: 8 }, (_, i) => [i + 1]);
team.getRange("E4:E11").values = masterDrivers.slice(0, 5).concat(["", "", ""]).map((d) => [d]);
team.getRange("E4:E11").dataValidation = { rule: { type: "list", formula1: "='Team & Settings'!$B$4:$B$23" } };
team.getRange("E4:E11").format.fill = inputFill;
team.getRange("D4:E11").format.borders = { preset: "inside", style: "thin", color: border };
team.getRange("G3:H3").values = [["Optional reference", "Value"]];
team.getRange("G3:H3").format = { fill: cyan, font: { bold: true, color: navy } };
team.getRange("G4:H8").values = [
  ["Refuelling rate (L/sec)", 2.55],
  ["Tyre change (secs)", 20],
  ["Max driver time (mins)", 480],
  ["Minimum rest (mins)", 0],
  ["Clock label", "Local"],
];
team.getRange("H4:H8").format.fill = inputFill;
team.getRange("G4:H8").format.borders = { preset: "inside", style: "thin", color: border };
team.getRange("D13:H13").merge();
team.getRange("D13").values = [["Choose only the drivers attending this event. The stint and stand-by dropdowns use this event roster, not the full team list."]];
team.getRange("D13:H13").format = { fill: paleBlue, font: { italic: true, color: muted }, wrapText: true };
team.getRange("D15:H15").merge();
team.getRange("D15").values = [["Optional settings are retained from the reference sheet; the main schedule uses the measured pit-loss input to stay simple."]];
team.getRange("D15:H15").format = { fill: paleBlue, font: { italic: true, color: muted }, wrapText: true };
team.getRange("A1:H23").format.font.name = "Aptos";
team.getRange("A:A").format.columnWidth = 8;
team.getRange("B:B").format.columnWidth = 30;
team.getRange("C:C").format.columnWidth = 5;
team.getRange("D:D").format.columnWidth = 12;
team.getRange("E:E").format.columnWidth = 30;
team.getRange("F:F").format.columnWidth = 5;
team.getRange("G:G").format.columnWidth = 30;
team.getRange("H:H").format.columnWidth = 18;

// ---------------- Car reference data ----------------
cars.getRange("A1:G1").values = [["Sim", "Car", "Class", "Reference capacity / limit", "Unit", "Source / scope", "Lookup key"]];
cars.getRange("A1:G1").format = { fill: navy, font: { bold: true, color: white }, wrapText: true };
const lmuSource = "https://lemansultimate.com/wp-content/uploads/2026/06/LMU_BOP_1.3.3_Marked-up.pdf";
const lmuCars = [
  ["LMU", "Alpine A424", "Hypercar", 913, "MJ", lmuSource],
  ["LMU", "Aston Martin Valkyrie AMR-LMH", "Hypercar", 894, "MJ", lmuSource],
  ["LMU", "BMW M Hybrid V8", "Hypercar", 905, "MJ", lmuSource],
  ["LMU", "Cadillac V-Series.R", "Hypercar", 904, "MJ", lmuSource],
  ["LMU", "Ferrari 499P", "Hypercar", 890, "MJ", lmuSource],
  ["LMU", "Genesis GMR-001", "Hypercar", 913, "MJ", lmuSource],
  ["LMU", "Glickenhaus SCG 007", "Hypercar", 913, "MJ", lmuSource],
  ["LMU", "Isotta Fraschini Tipo 6", "Hypercar", 923, "MJ", lmuSource],
  ["LMU", "Lamborghini SC63", "Hypercar", 908, "MJ", lmuSource],
  ["LMU", "Peugeot 9X8", "Hypercar", 894, "MJ", lmuSource],
  ["LMU", "Peugeot 9X8 Evo", "Hypercar", 885, "MJ", lmuSource],
  ["LMU", "Porsche 963", "Hypercar", 910, "MJ", lmuSource],
  ["LMU", "Toyota GR010-Hybrid", "Hypercar", 900, "MJ", lmuSource],
  ["LMU", "Vanwall Vandervell 680", "Hypercar", 920, "MJ", lmuSource],
  ["LMU", "Aston Martin Vantage AMR LMGT3", "LMGT3", 675, "MJ", lmuSource],
  ["LMU", "BMW M4 LMGT3", "LMGT3", 668, "MJ", lmuSource],
  ["LMU", "Chevrolet Corvette Z06 LMGT3.R", "LMGT3", 703, "MJ", lmuSource],
  ["LMU", "Ferrari 296 LMGT3", "LMGT3", 672, "MJ", lmuSource],
  ["LMU", "Ford Mustang LMGT3", "LMGT3", 711, "MJ", lmuSource],
  ["LMU", "Lamborghini Huracan LMGT3 Evo2", "LMGT3", 684, "MJ", lmuSource],
  ["LMU", "Lexus RC F LMGT3", "LMGT3", 666, "MJ", lmuSource],
  ["LMU", "McLaren 720S LMGT3 Evo", "LMGT3", 673, "MJ", lmuSource],
  ["LMU", "Mercedes-AMG LMGT3", "LMGT3", 666, "MJ", lmuSource],
  ["LMU", "Porsche 911 GT3 R LMGT3", "LMGT3", 671, "MJ", lmuSource],
  ["LMU", "Oreca 07 Gibson (WEC)", "LMP2", 63, "L", lmuSource],
  ["LMU", "Aston Martin Vantage GTE", "GTE", 97, "L", lmuSource],
  ["LMU", "Chevrolet Corvette C8.R", "GTE", 91, "L", lmuSource],
  ["LMU", "Ferrari 488 GTE Evo", "GTE", 84, "L", lmuSource],
  ["LMU", "Porsche 911 RSR-19", "GTE", 99, "L", lmuSource],
  ["LMU", "Oreca 07 Gibson (ELMS)", "LMP2", 75, "L", lmuSource],
];
const irCarListSource = "https://www.iracing.com/cars/";
const manualSource = "https://www.iracing.com/resources/user-manuals/";
const irCars = [
  ["iRacing", "Acura NSX GT3 EVO 22", "GT3", null, "L", irCarListSource],
  ["iRacing", "Aston Martin Vantage GT3 EVO", "GT3", null, "L", irCarListSource],
  ["iRacing", "Audi R8 LMS EVO II GT3", "GT3", null, "L", irCarListSource],
  ["iRacing", "BMW M4 GT3 EVO", "GT3", 120, "L", manualSource],
  ["iRacing", "Chevrolet Corvette Z06 GT3.R", "GT3", null, "L", irCarListSource],
  ["iRacing", "Ferrari 296 GT3", "GT3", 104, "L", "https://s100.iracing.com/wp-content/uploads/2024/01/Ferrari-296-GT3-V2.pdf"],
  ["iRacing", "Ford Mustang GT3", "GT3", null, "L", irCarListSource],
  ["iRacing", "Lamborghini Huracan GT3 EVO", "GT3", 120, "L", manualSource],
  ["iRacing", "McLaren 720S GT3 EVO", "GT3", null, "L", irCarListSource],
  ["iRacing", "Mercedes-AMG GT3 2020", "GT3", 120, "L", manualSource],
  ["iRacing", "Porsche 911 GT3 R (992)", "GT3", 120, "L", manualSource],
  ["iRacing", "Acura ARX-06 GTP", "GTP", null, "L", irCarListSource],
  ["iRacing", "BMW M Hybrid V8 Evo", "GTP", null, "L", irCarListSource],
  ["iRacing", "Cadillac V-Series.R GTP", "GTP", null, "L", irCarListSource],
  ["iRacing", "Ferrari 499P", "GTP", null, "L", irCarListSource],
  ["iRacing", "Porsche 963 GTP", "GTP", null, "L", irCarListSource],
  ["iRacing", "Dallara P217 LMP2", "LMP2", null, "L", irCarListSource],
  ["iRacing", "Ligier JS P320", "LMP3", null, "L", irCarListSource],
];
const allCars = [...lmuCars, ...irCars];
cars.getRange(`A2:F${allCars.length + 1}`).values = allCars;
for (let r = 2; r <= allCars.length + 1; r++) cars.getRange(`G${r}`).formulas = [[`=A${r}&\"|\"&B${r}`]];
cars.getRange(`A2:G${allCars.length + 1}`).format.borders = { preset: "inside", style: "thin", color: border };
cars.getRange(`A2:G${lmuCars.length + 1}`).format.fill = "#E8F4FA";
cars.getRange(`A${lmuCars.length + 2}:G${allCars.length + 1}`).format.fill = "#F4E9FF";
cars.getRange(`D2:D${allCars.length + 1}`).format.numberFormat = "0.0";
cars.getRange("A1:G60").format.font.name = "Aptos";
cars.getRange("A:A").format.columnWidth = 12;
cars.getRange("B:B").format.columnWidth = 38;
cars.getRange("C:C").format.columnWidth = 15;
cars.getRange("D:D").format.columnWidth = 24;
cars.getRange("E:E").format.columnWidth = 10;
cars.getRange("F:F").format.columnWidth = 72;
cars.getRange("G:G").format.columnWidth = 48;
cars.freezePanes.freezeRows(1);

// ---------------- Quick guide ----------------
guide.getRange("A1:F1").merge();
guide.getRange("A1").values = [["QUICK GUIDE"]];
guide.getRange("A1:F1").format = { fill: navy, font: { bold: true, color: white, size: 16 } };
guide.getRange("A3:B3").values = [["Step", "What to do"]];
guide.getRange("A3:B3").format = { fill: cyan, font: { bold: true, color: navy } };
guide.getRange("A4:B10").values = [
  [1, "Maintain the master driver list, then choose this race's event roster on Team & Settings."],
  [2, "Pick the sim, then pick a car from that sim's dropdown."],
  [3, "Confirm the usable tank/energy in the actual race session and enter the override."],
  [4, "Enter an average race lap and measured fuel per lap from a representative run."],
  [5, "Set race date, local start time, race length, reserve laps, max stint and measured pit loss."],
  [6, "Assign a driver and stand-by driver to each active stint row."],
  [7, "Use the full date/time in Start and End to handle overnight races and date changes."],
];
guide.getRange("A4:B10").format.borders = { preset: "inside", style: "thin", color: border };
guide.getRange("A12:F12").merge();
guide.getRange("A12").values = [["Included from the reference workbook"]];
guide.getRange("A12:F12").format = { fill: cyan, font: { bold: true, color: navy } };
guide.getRange("A13:F18").merge(true);
guide.getRange("A13:F18").values = [
  ["Driver rotation, stand-by driver, race clock conversion, reserve fuel, max stint, pit-loss allowance, driver totals, optional refuelling rate, tyre-change time and max driver time."],
  ["Fuel-per-lap remains an input because it changes with track, conditions, setup, traffic and driver."],
  ["LMU Spa 2026 BoP uses maximum stint energy for Hypercar/LMGT3, not a simple fuel-tank litre figure."],
  ["Blank iRacing capacities mean no reliable capacity was found in the official source used—confirm the garage value and use the override."],
  ["All sourced values are references only; event rules, fuel restrictions and BoP can change."],
  ["Car-list sources: https://lemansultimate.com/cars/ and https://www.iracing.com/cars/"],
];
guide.getRange("A13:F18").format = { fill: paleBlue, font: { color: textDark }, wrapText: true, borders: { preset: "inside", style: "thin", color: border } };
guide.getRange("A1:F18").format.font.name = "Aptos";
guide.getRange("A:A").format.columnWidth = 9;
guide.getRange("B:B").format.columnWidth = 92;
guide.getRange("C:F").format.columnWidth = 12;
guide.getRange("B4:B10").format.wrapText = true;

// Workbook verification snapshots and preview renders.
const keyCheck = await wb.inspect({ kind: "table", sheetId: "Stint Planner", range: "A4:J30", include: "values,formulas", tableMaxRows: 30, tableMaxCols: 10, maxChars: 14000 });
console.log(keyCheck.ndjson);
const errors = await wb.inspect({ kind: "match", searchTerm: "#REF!|#DIV/0!|#VALUE!|#NAME\\?|#N/A", options: { useRegex: true, maxResults: 200 }, summary: "final formula error scan" });
console.log(errors.ndjson);
for (const sh of [planner, team, cars, guide]) {
  const preview = await wb.render({ sheetName: sh.name, autoCrop: "all", scale: 1, format: "png" });
  await fs.writeFile(`${outputDir}/${sh.name.replace(/[^a-z0-9_-]/gi, "_")}.png`, new Uint8Array(await preview.arrayBuffer()));
}
const out = await SpreadsheetFile.exportXlsx(wb);
await out.save(outputPath);
console.log(`SAVED ${outputPath}`);
