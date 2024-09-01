import { isBefore, isThisWeek } from "date-fns";

export function isJSON(item) {
  item = typeof item !== "string" ? JSON.stringify(item) : item;

  try {
    item = JSON.parse(item);
  } catch (e) {
    return false;
  }

  if (typeof item === "object" && item !== null) {
    return true;
  }

  return false;
}

export function getDateStatus(created, updated) {
  const createdDate = new Date(created * 1000);
  const updatedDate = new Date(updated * 1000);
  const isNew = isThisWeek(createdDate);
  const isUpdated = isBefore(createdDate, updatedDate);
  // const display = format(new Date(created * 1000), "Y-M-dd H:i:s");
  // console.log(format(new Date(created * 1000), "yyyyMMdd"));

  // console.log(createdDate);
  // console.log(updatedDate);
  // console.log("isNew:", isNew);
  // console.log("isUpdated", isUpdated);

  if (isNew && !isUpdated) {
    return "New";
  } else if (isUpdated) {
    return "Updated";
  }
}

export function colorSchemeMap(colorScheme) {
  switch (colorScheme) {
    case "gray":
      return "gray.900";
    case "red":
      return "white";
    case "orange":
      return "white";
    case "yellow":
      return "gray.900";
    case "green":
      return "white";
    case "teal":
      return "white";
    case "blue":
      return "white";
    case "cyan":
      return "gray.900";
    case "purple":
      return "white";
    case "pink":
      return "white";
    default:
      return "gray.900";
  }
}

// export function colorSchemeMap(colorName) {
//   switch (colorName) {
//     case "gray":
//       return "#718096";
//     case "red":
//       return "#E53E3E";
//     case "orange":
//       return "#DD6B20";
//     case "yellow":
//       return "#D69E2E";
//     case "green":
//       return "#38A169";
//     case "teal":
//       return "#319795";
//     case "blue":
//       return "#3182CE";
//     case "cyan":
//       return "#00B5D8";
//     case "purple":
//       return "#805AD5";
//     case "pink":
//       return "#D53F8C";
//     default:
//       return "#000000";
//   }
// }
