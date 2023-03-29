import type { parsedPdfDataInput } from '../services/pdfData.service';

export function validateData(pdfData: parsedPdfDataInput) {
  const errorBag = [];

  if (!pdfData.surname) {
    errorBag.push('Please type your Surname.');
  }
  if (!pdfData.given_name) {
    errorBag.push('Please type your Given Name.');
  }
  if (pdfData.master_thesis) {
    if (!pdfData.master_thesis.theses.length) {
      errorBag.push('Please select a broad topic for your MSc Thesis.');
    }
  } else {
    errorBag.push('Please select a broad topic for your MSc Thesis.');
  }

  if (pdfData.statistics.moduleGroupCount) {
    for (const group of pdfData.statistics.moduleGroupCount) {
      if (group.required_courses_count > group.count) {
        errorBag.push(`You have not selected enough modules in ${group.name}. Please correct`);
      }
    }
  }
  if (pdfData.statistics.moduleGroupCount.findIndex((group) => group.type === 3) === -1) {
    console.log(pdfData.statistics.moduleGroupCount.findIndex((group) => group.type === 3));
    if (pdfData.statistics.cluster < 3) {
      errorBag.push('You need to select at least three cluster-specific modules. Please correct.');
    }
  }
  if (pdfData.statistics.ects < 50) {
    errorBag.push('You have selected modules worth fewer than 50 ECTS.');
  }
  return {
    amount: errorBag.length,
    errors: errorBag
  };
}
