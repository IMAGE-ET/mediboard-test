<?php /** $Id$ **/

/**
 *  @package Mediboard
 *  @subpackage dicom
 *  @version $Revision$
 *  @author SARL OpenXtrem
 */

/**
 * The DICOM dictionnary
 */
class CDicomDictionary {
  
  /**
   * DICOM SOP classes
   *
   * @see DICOM Standard PS 3.6 Annex A
   * 
   * @var array
   */
  protected static $sop_classes = array(
    '1.2.840.10008.1.1' => 'Verification SOP Class',
    '1.2.840.10008.3.1.1.1' => 'DICOM Application Context Name',
    '1.2.840.10008.5.1.4.31' => 'Modality Worklist Information Model - FIND',
  );
  
  /**
   * The DICOM Network transfer syntaxes
   * 
   * @see DICOM Standard PS 3.6 Annex A
   * 
   * @var array
   */
  protected static $transfer_syntaxes = array(
    '1.2.840.10008.1.2' 	=> 'Implicit VR Little Endian',
    '1.2.840.10008.1.2.1' => 'Explicit VR Little Endian',
    '1.2.840.10008.1.2.2' => 'Explicit VR Big Endian',
  );
  
  /**
   * The value representations
   * Each array contains the full name, the maximum length (in bytes), and if the length is fixed (1) or not (0)
   * 
   * @see DICOM Standard PS 3.5 Section 6.2
   * 
   * @var array
   */
  protected static $value_representations = array(
   'AE'=> array('Name'=>'Application Entity'							, 'Length' => 16				, 'Fixed' => 0),
   'AS'=> array('Name'=>'Age String'											, 'Length' => 4					, 'Fixed' => 1),
   'AT'=> array('Name'=>'Attribute Tag'										, 'Length' => 4					, 'Fixed' => 1),
   'CS'=> array('Name'=>'Code String'											, 'Length' => 16				, 'Fixed' => 0),
   'DA'=> array('Name'=>'Date'														, 'Length' => 8					, 'Fixed' => 1),
   'DS'=> array('Name'=>'Decimal String'									, 'Length' => 16				, 'Fixed' => 0),
   'DT'=> array('Name'=>'Date Time'												, 'Length' => 26				, 'Fixed' => 0),
   'FL'=> array('Name'=>'Floating Point Single'						, 'Length' => 4					, 'Fixed' => 1),
   'FD'=> array('Name'=>'Floating Point Double'						, 'Length' => 8					, 'Fixed' => 1),
   'IS'=> array('Name'=>'Integer String'									, 'Length' => 12				, 'Fixed' => 0),
   'LO'=> array('Name'=>'Long String'											, 'Length' => 64				, 'Fixed' => 0),
   'LT'=> array('Name'=>'Long Text'												, 'Length' => 10240			, 'Fixed' => 0),
   'OB'=> array('Name'=>'Other Byte String'								, 'Length' => 0					, 'Fixed' => 0),
   'OF'=> array('Name'=>'Other Float String'							, 'Length' => 4294967292, 'Fixed' => 0),
   'OX'=> array('Name'=>'Mixed. Other {Byte|Word} String'	, 'Length' => 0					, 'Fixed' => 0),
   'OW'=> array('Name'=>'Other Word String'								, 'Length' => 0					, 'Fixed' => 0),
   'PN'=> array('Name'=>'Person Name'											, 'Length' => 64				, 'Fixed' => 0),
   'SH'=> array('Name'=>'Short String'										, 'Length' => 16				, 'Fixed' => 0),
   'SL'=> array('Name'=>'Signed Long'											, 'Length' => 4					, 'Fixed' => 1),
   'SQ'=> array('Name'=>'Sequence of Items'								, 'Length' => 0					, 'Fixed' => 0),
   'SS'=> array('Name'=>'Signed Short'										, 'Length' => 2					, 'Fixed' => 1),
   'ST'=> array('Name'=>'Short Text'											, 'Length' => 1024			, 'Fixed' => 0),
   'TM'=> array('Name'=>'Time'														, 'Length' => 16				, 'Fixed' => 0),
   'UI'=> array('Name'=>'Unique Identifier UID'						, 'Length' => 64				, 'Fixed' => 0),
   'UL'=> array('Name'=>'Unsigned Long'										, 'Length' => 4					, 'Fixed' => 1),
   'UN'=> array('Name'=>'Unknown'													, 'Length' => 0					, 'Fixed' => 0),
   'US'=> array('Name'=>'Unsigned Short'									, 'Length' => 2					, 'Fixed' => 1),
   'UT'=> array('Name'=>'Unlimited Text'									, 'Length' => 4294967294, 'Fixed' => 0)
  );
  
  /**
   * DICOM data sets.
   * 
   * The first key is the group number, the second the element number.
   * Foreach element, the first element is the value representation,
   * the second the value multiplicity, and the last is the name
   * 
   * @see DICOM Standard PS 3.6 Section 6
   * 
   * @var array
   */
  protected static $data_sets = array(
    0x0000 => array(
      0x0000 => array('UL', '1',    'GroupLength'),
      0x0001 => array('UL', '1',    'CommandLengthToEnd'),
      0x0002 => array('UI', '1',    'AffectedSOPClassUID'),
      0x0003 => array('UI', '1',    'RequestedSOPClassUID'),
      0x0010 => array('CS', '1',    'CommandRecognitionCode'),
      0x0100 => array('US', '1',    'CommandField'),
      0x0110 => array('US', '1',    'MessageID'),
      0x0120 => array('US', '1',    'MessageIDBeingRespondedTo'),
      0x0200 => array('AE', '1',    'Initiator'),
      0x0300 => array('AE', '1',    'Receiver'),
      0x0400 => array('AE', '1',    'FindLocation'),
      0x0600 => array('AE', '1',    'MoveDestination'),
      0x0700 => array('US', '1',    'Priority'),
      0x0800 => array('US', '1',    'DataSetType'),
      0x0850 => array('US', '1',    'NumberOfMatches'),
      0x0860 => array('US', '1',    'ResponseSequenceNumber'),
      0x0900 => array('US', '1',    'Status'),
      0x0901 => array('AT', '1-n',  'OffendingElement'),
      0x0902 => array('LO', '1',    'ErrorComment'),
      0x0903 => array('US', '1',    'ErrorID'),
      0x0904 => array('OT', '1-n',  'ErrorInformation'),
      0x1000 => array('UI', '1',    'AffectedSOPInstanceUID'),
      0x1001 => array('UI', '1',    'RequestedSOPInstanceUID'),
      0x1002 => array('US', '1',    'EventTypeID'),
      0x1003 => array('OT', '1-n',  'EventInformation'),
      0x1005 => array('AT', '1-n',  'AttributeIdentifierList'),
      0x1007 => array('AT', '1-n',  'ModificationList'),
      0x1008 => array('US', '1',    'ActionTypeID'),
      0x1009 => array('OT', '1-n',  'ActionInformation'),
      0x1013 => array('UI', '1-n',  'SuccessfulSOPInstanceUIDList'),
      0x1014 => array('UI', '1-n',  'FailedSOPInstanceUIDList'),
      0x1015 => array('UI', '1-n',  'WarningSOPInstanceUIDList'),
      0x1020 => array('US', '1',    'NumberOfRemainingSuboperations'),
      0x1021 => array('US', '1',    'NumberOfCompletedSuboperations'),
      0x1022 => array('US', '1',    'NumberOfFailedSuboperations'),
      0x1023 => array('US', '1',    'NumberOfWarningSuboperations'),
      0x1030 => array('AE', '1',    'MoveOriginatorApplicationEntityTitle'),
      0x1031 => array('US', '1',    'MoveOriginatorMessageID'),
      0x4000 => array('AT', '1',    'DialogReceiver'),
      0x4010 => array('AT', '1',    'TerminalType'),
      0x5010 => array('SH', '1',    'MessageSetID'),
      0x5020 => array('SH', '1',    'EndMessageSet'),
      0x5110 => array('AT', '1',    'DisplayFormat'),
      0x5120 => array('AT', '1',    'PagePositionID'),
      0x5130 => array('CS', '1',    'TextFormatID'),
      0x5140 => array('CS', '1',    'NormalReverse'),
      0x5150 => array('CS', '1',    'AddGrayScale'),
      0x5160 => array('CS', '1',    'Borders'),
      0x5170 => array('IS', '1',    'Copies'),
      0x5180 => array('CS', '1',    'OldMagnificationType'),
      0x5190 => array('CS', '1',    'Erase'),
      0x51A0 => array('CS', '1',    'Print'),
      0x51B0 => array('US', '1-n',  'Overlays'),
    ),
    
    0x0008 => array(
      0x0000 => array('UL', '1',    'IdentifyingGroupLength', 'RET'),
      0x0001 => array('UL', '1',    'LengthToEnd', 'RET'),
      0x0005 => array('CS', '1-n',  'SpecificCharacterSet'),
      0x0006 => array('SQ', '1',    'LanguageCodeSequence'),
      0x0008 => array('CS', '2-n',  'ImageType'),
      0x000A => array('US', '1',    'SequenceItemNumber'), // NA
      0x0010 => array('CS', '1',    'RecognitionCode', 'RET'),
      0x0012 => array('DA', '1',    'InstanceCreationDate'),
      0x0013 => array('TM', '1',    'InstanceCreationTime'),
      0x0014 => array('UI', '1',    'InstanceCreatorUID'),
      0x0016 => array('UI', '1',    'SOPClassUID'),
      0x0018 => array('UI', '1',    'SOPInstanceUID'),
      0x001A => array('UI', '1-n',  'RelatedGeneralSOPClassUID'),
      0x001B => array('UI', '1',    'OriginalSpecializedSOPClassUID'),
      0x0020 => array('DA', '1',    'StudyDate'),
      0x0021 => array('DA', '1',    'SeriesDate'),
      0x0022 => array('DA', '1',    'AcquisitionDate'),
      0x0023 => array('DA', '1',    'ContentDate'),
      0x0024 => array('DA', '1',    'OverlayDate', 'RET'),
      0x0025 => array('DA', '1',    'CurveDate', 'RET'),
      0x002A => array('DT', '1',    'AcquisitionDatetime'),
      0x0030 => array('TM', '1',    'StudyTime'),
      0x0031 => array('TM', '1',    'SeriesTime'),
      0x0032 => array('TM', '1',    'AcquisitionTime'),
      0x0033 => array('TM', '1',    'ContentTime'),
      0x0034 => array('TM', '1',    'OverlayTime', 'RET'),
      0x0035 => array('TM', '1',    'CurveTime', 'RET'),
      0x0040 => array('US', '1',    'DataSetType', 'RET'),
      0x0041 => array('LO', '1',    'DataSetSubtype', 'RET'),
      0x0042 => array('CS', '1',    'NuclearMedicineSeriesType', 'RET'),
      0x0050 => array('SH', '1',    'AccessionNumber'),
      0x0051 => array('SQ', '1',    'IssuerOfAccessionNumberSequence'),
      0x0052 => array('CS', '1',    'QueryRetrieveLevel'),
      0x0054 => array('AE', '1-n',  'RetrieveAETitle'),
      0x0056 => array('CS', '1',    'InstanceAvailability'),
      0x0058 => array('UI', '1-n',  'FailedSOPInstanceUIDList'),
      0x0060 => array('CS', '1',    'Modality'),
      0x0061 => array('CS', '1-n',  'ModalitiesInStudy'),
      0x0062 => array('UI', '1-n',  'SOPClassesInStudy'),
      0x0064 => array('CS', '1',    'ConversionType'),
      0x0068 => array('CS', '1',    'PresentationIntentType'),
      0x0070 => array('LO', '1',    'Manufacturer'),
      0x0080 => array('LO', '1',    'InstitutionName'),
      0x0081 => array('ST', '1',    'InstitutionAddress'),
      0x0082 => array('SQ', '1',    'InstitutionCodeSequence'),
      0x0090 => array('PN', '1',    'ReferringPhysicianName'),
      0x0092 => array('ST', '1',    'ReferringPhysicianAddress'),
      0x0094 => array('SH', '1-n',  'ReferringPhysicianTelephoneNumbers'),
      0x0096 => array('SQ', '1',    'ReferringPhysicianIdentificationSequence'),
      0x0100 => array('SH', '1',    'CodeValue'),
      0x0102 => array('SH', '1',    'CodingSchemeDesignator'),
      0x0103 => array('SH', '1',    'CodingSchemeVersion'),
      0x0104 => array('LO', '1',    'CodeMeaning'),
      0x0105 => array('CS', '1',    'MappingResource'),
      0x0106 => array('DT', '1',    'ContextGroupVersion'),
      0x0107 => array('DT', '1',    'ContextGroupLocalVersion'),
      0x010B => array('CS', '1',    'ContextGroupExtensionFlag'),
      0x010C => array('UI', '1',    'CodingSchemeUID'),
      0x010D => array('UI', '1',    'ContextGroupExtensionCreatorUID'),
      0x010F => array('CS', '1',    'ContextIdentifier'),
      0x0110 => array('SQ', '1',    'CodingSchemeIdentificationSequence'),
      0x0112 => array('LO', '1',    'CodingSchemeRegistry'),
      0x0114 => array('ST', '1',    'CodingSchemeExternalID'),
      0x0115 => array('ST', '1',    'CodingSchemeName'),
      0x0116 => array('ST', '1',    'CodingSchemeResponsibleOrganization'),
      0x0117 => array('UI', '1',    'ContextUID'),
      0x0201 => array('SH', '1',    'TimezoneOffsetFromUTC'),
      0x1000 => array('AE', '1',    'NetworkID', 'RET'),
      0x1010 => array('SH', '1',    'StationName'),
      0x1030 => array('LO', '1',    'StudyDescription'),
      0x1032 => array('SQ', '1',    'ProcedureCodeSequence'),
      0x103E => array('LO', '1',    'SeriesDescription'),
      0x103F => array('SQ', '1',    'SeriesDescriptionCodeSequence'),
      0x1040 => array('LO', '1',    'InstitutionalDepartmentName'),
      0x1048 => array('PN', '1-n',  'PhysiciansOfRecord'),
      0x1049 => array('SQ', '1',    'PhysiciansOfRecordIdentificationSequence'),
      0x1050 => array('PN', '1-n',  'PerformingPhysicianName'),
      0x1052 => array('SQ', '1',    'PerformingPhysicianIdentificationSequence'),
      0x1060 => array('PN', '1-n',  'NameOfPhysiciansReadingStudy'),
      0x1062 => array('SQ', '1',    'PhysiciansReadingStudyIdentificationSequence'),
      0x1070 => array('PN', '1-n',  'OperatorsName'),
      0x1072 => array('SQ', '1',    'OperatorIdentificationSequence'),
      0x1080 => array('LO', '1-n',  'AdmittingDiagnosesDescription'),
      0x1084 => array('SQ', '1',    'AdmittingDiagnosesCodeSequence'),
      0x1090 => array('LO', '1',    'ManufacturerModelName'),
      0x1100 => array('SQ', '1',    'ReferencedResultsSequence', 'RET'),
      0x1110 => array('SQ', '1',    'ReferencedStudySequence'),
      0x1111 => array('SQ', '1',    'ReferencedPerformedProcedureStepSequence'),
      0x1115 => array('SQ', '1',    'ReferencedSeriesSequence'),
      0x1120 => array('SQ', '1',    'ReferencedPatientSequence'),
      0x1125 => array('SQ', '1',    'ReferencedVisitSequence'),
      0x1130 => array('SQ', '1',    'ReferencedOverlaySequence', 'RET'),
      0x1134 => array('SQ', '1',    'ReferencedStereometricInstanceSequence'),
      0x113A => array('SQ', '1',    'ReferencedWaveformSequence'),
      0x1140 => array('SQ', '1',    'ReferencedImageSequence'),
      0x1145 => array('SQ', '1',    'ReferencedCurveSequence', 'RET'),
      0x114A => array('SQ', '1',    'ReferencedInstanceSequence'),
      0x114B => array('SQ', '1',    'ReferencedRealWorldValueMappingInstanceSequence'),
      0x1150 => array('UI', '1',    'ReferencedSOPClassUID'),
      0x1155 => array('UI', '1',    'ReferencedSOPInstanceUID'),
      0x115A => array('UI', '1-n',  'SOPClassesSupported'),
      0x1160 => array('IS', '1-n',  'ReferencedFrameNumber'),
      0x1161 => array('UL', '1-n',  'SimpleFrameList'),
      0x1162 => array('UL', '3-3n', 'CalculatedFrameList'),
      0x1163 => array('FD', '2',    'TimeRange'),
      0x1164 => array('SQ', '1',    'FrameExtractionSequence'),
      0x1167 => array('UI', '1',    'MultiFrameSourceSOPInstanceUID'),
      0x1195 => array('UI', '1',    'TransactionUID'),
      0x1197 => array('US', '1',    'FailureReason'),
      0x1198 => array('SQ', '1',    'FailedSOPSequence'),
      0x1199 => array('SQ', '1',    'ReferencedSOPSequence'),
      0x1200 => array('SQ', '1',    'StudiesContainingOtherReferencedInstancesSequence'),
      0x1250 => array('SQ', '1',    'RelatedSeriesSequence'),
      0x2110 => array('CS', '1',    'LossyImageCompressionRetired', 'RET'),
      0x2111 => array('ST', '1',    'DerivationDescription'),
      0x2112 => array('SQ', '1',    'SourceImageSequence'),
      0x2120 => array('SH', '1',    'StageName'),
      0x2122 => array('IS', '1',    'StageNumber'),
      0x2124 => array('IS', '1',    'NumberOfStages'),
      0x2127 => array('SH', '1',    'ViewName'),
      0x2128 => array('IS', '1',    'ViewNumber'),
      0x2129 => array('IS', '1',    'NumberOfEventTimers'),
      0x212A => array('IS', '1',    'NumberOfViewsInStage'),
      0x2130 => array('DS', '1-n',  'EventElapsedTimes'),
      0x2132 => array('LO', '1-n',  'EventTimerNames'),
      0x2133 => array('SQ', '1',    'EventTimerSequence'),
      0x2134 => array('FD', '1',    'EventTimeOffset'),
      0x2135 => array('SQ', '1',    'EventCodeSequence'),
      0x2142 => array('IS', '1',    'StartTrim'),
      0x2143 => array('IS', '1',    'StopTrim'),
      0x2144 => array('IS', '1',    'RecommendedDisplayFrameRate'),
      0x2200 => array('CS', '1',    'TransducerPosition', 'RET'),
      0x2204 => array('CS', '1',    'TransducerOrientation', 'RET'),
      0x2208 => array('CS', '1',    'AnatomicStructure', 'RET'),
      0x2218 => array('SQ', '1',    'AnatomicRegionSequence'),
      0x2220 => array('SQ', '1',    'AnatomicRegionModifierSequence'),
      0x2228 => array('SQ', '1',    'PrimaryAnatomicStructureSequence'),
      0x2229 => array('SQ', '1',    'AnatomicStructureSpaceOrRegionSequence'),
      0x2230 => array('SQ', '1',    'PrimaryAnatomicStructureModifierSequence'),
      0x2240 => array('SQ', '1',    'TransducerPositionSequence', 'RET'),
      0x2242 => array('SQ', '1',    'TransducerPositionModifierSequence', 'RET'),
      0x2244 => array('SQ', '1',    'TransducerOrientationSequence', 'RET'),
      0x2246 => array('SQ', '1',    'TransducerOrientationModifierSequence', 'RET'),
      0x2251 => array('SQ', '1',    'AnatomicStructureSpaceOrRegionCodeSequenceTrial', 'RET'),
      0x2253 => array('SQ', '1',    'AnatomicPortalOfEntranceCodeSequenceTrial', 'RET'),
      0x2255 => array('SQ', '1',    'AnatomicApproachDirectionCodeSequenceTrial', 'RET'),
      0x2256 => array('ST', '1',    'AnatomicPerspectiveDescriptionTrial', 'RET'),
      0x2257 => array('SQ', '1',    'AnatomicPerspectiveCodeSequenceTrial', 'RET'),
      0x2258 => array('ST', '1',    'AnatomicLocationOfExaminingInstrumentDescriptionTrial', 'RET'),
      0x2259 => array('SQ', '1',    'AnatomicLocationOfExaminingInstrumentCodeSequenceTrial', 'RET'),
      0x225A => array('SQ', '1',    'AnatomicStructureSpaceOrRegionModifierCodeSequenceTrial', 'RET'),
      0x225C => array('SQ', '1',    'OnAxisBackgroundAnatomicStructureCodeSequenceTrial', 'RET'),
      0x3001 => array('SQ', '1',    'AlternateRepresentationSequence'),
      0x3010 => array('UI', '1',    'IrradiationEventUID'),
      0x4000 => array('LT', '1',    'IdentifyingComments', 'RET'),
      0x9007 => array('CS', '4',    'FrameType'),
      0x9092 => array('SQ', '1',    'ReferencedImageEvidenceSequence'),
      0x9121 => array('SQ', '1',    'ReferencedRawDataSequence'),
      0x9123 => array('UI', '1',    'CreatorVersionUID'),
      0x9124 => array('SQ', '1',    'DerivationImageSequence'),
      0x9154 => array('SQ', '1',    'SourceImageEvidenceSequence'),
      0x9205 => array('CS', '1',    'PixelPresentation'),
      0x9206 => array('CS', '1',    'VolumetricProperties'),
      0x9207 => array('CS', '1',    'VolumeBasedCalculationTechnique'),
      0x9208 => array('CS', '1',    'ComplexImageComponent'),
      0x9209 => array('CS', '1',    'AcquisitionContrast'),
      0x9215 => array('SQ', '1',    'DerivationCodeSequence'),
      0x9237 => array('SQ', '1',    'ReferencedPresentationStateSequence'),
      0x9410 => array('SQ', '1',    'ReferencedOtherPlaneSequence'),
      0x9458 => array('SQ', '1',    'FrameDisplaySequence'),
      0x9459 => array('FL', '1',    'RecommendedDisplayFrameRateInFloat'),
      0x9460 => array('CS', '1',    'SkipFrameRangeFlag'),
    ),
    
    0x0010 => array(
      0x0000 => array('UL', '1',    'PatientGroupLength', 'RET'),
      0x0010 => array('PN', '1',    'PatientName'),
      0x0020 => array('LO', '1',    'PatientID'),
      0x0021 => array('LO', '1',    'IssuerOfPatientID'),
      0x0022 => array('CS', '1',    'TypeOfPatientID'),
      0x0024 => array('SQ', '1',    'IssuerOfPatientIDQualifiersSequence'),
      0x0030 => array('DA', '1',    'PatientBirthDate'),
      0x0032 => array('TM', '1',    'PatientBirthTime'),
      0x0040 => array('CS', '1',    'PatientSex'),
      0x0050 => array('SQ', '1',    'PatientInsurancePlanCodeSequence'),
      0x0101 => array('SQ', '1',    'PatientPrimaryLanguageCodeSequence'),
      0x0102 => array('SQ', '1',    'PatientPrimaryLanguageModifierCodeSequence'),
      0x1000 => array('LO', '1-n',  'OtherPatientIDs'),
      0x1001 => array('PN', '1-n',  'OtherPatientNames'),
      0x1002 => array('SQ', '1',    'OtherPatientIDsSequence'),
      0x1005 => array('PN', '1',    'PatientBirthName'),
      0x1010 => array('AS', '1',    'PatientAge'),
      0x1020 => array('DS', '1',    'PatientSize'),
      0x1030 => array('DS', '1',    'PatientWeight'),
      0x1040 => array('LO', '1',    'PatientAddress'),
      0x1050 => array('LO', '1-n',  'InsurancePlanIdentification', 'RET'),
      0x1060 => array('PN', '1',    'PatientMotherBirthName'),
      0x1080 => array('LO', '1',    'MilitaryRank'),
      0x1081 => array('LO', '1',    'BranchOfService'),
      0x1090 => array('LO', '1',    'MedicalRecordLocator'),
      0x2000 => array('LO', '1-n',  'MedicalAlerts'),
      0x2110 => array('LO', '1-n',  'Allergies'),
      0x2150 => array('LO', '1',    'CountryOfResidence'),
      0x2152 => array('LO', '1',    'RegionOfResidence'),
      0x2154 => array('SH', '1-n',  'PatientTelephoneNumbers'),
      0x2160 => array('SH', '1',    'EthnicGroup'),
      0x2180 => array('SH', '1',    'Occupation'),
      0x21A0 => array('CS', '1',    'SmokingStatus'),
      0x21B0 => array('LT', '1',    'AdditionalPatientHistory'),
      0x21C0 => array('US', '1',    'PregnancyStatus'),
      0x21D0 => array('DA', '1',    'LastMenstrualDate'),
      0x21F0 => array('LO', '1',    'PatientReligiousPreference'),
      0x2201 => array('LO', '1',    'PatientSpeciesDescription'),
      0x2202 => array('SQ', '1',    'PatientSpeciesCodeSequence'),
      0x2203 => array('CS', '1',    'PatientSexNeutered'),
      0x2210 => array('CS', '1',    'AnatomicalOrientationType'),
      0x2292 => array('LO', '1',    'PatientBreedDescription'),
      0x2293 => array('SQ', '1',    'PatientBreedCodeSequence'),
      0x2294 => array('SQ', '1',    'BreedRegistrationSequence'),
      0x2295 => array('LO', '1',    'BreedRegistrationNumber'),
      0x2296 => array('SQ', '1',    'BreedRegistryCodeSequence'),
      0x2297 => array('PN', '1',    'ResponsiblePerson'),
      0x2298 => array('CS', '1',    'ResponsiblePersonRole'),
      0x2299 => array('LO', '1',    'ResponsibleOrganization'),
      0x4000 => array('LT', '1',    'PatientComments'),
      0x9431 => array('FL', '1',    'ExaminedBodyThickness'),
    ),
  );
  
  /**
   * Get the name of the SOP class
   * 
   * @param string $uid The UID of the SOP class
   * 
   * @return string
   */
  static function getSOPClass($uid) {
    return self::$sop_classes[$uid];
  }
  
  /**
   * Get the name of a transfer syntax
   * 
   * @param string $uid The UID of the transfer syntax
   * 
   * @return string
   */
  static function getTransferSyntaxName($uid) {
    return self::$transfer_syntaxes[$uid];
  }
  
  /**
   * Get the characteristics of a value representations
   * 
   * @param string $vr_name The name of the value representation
   * 
   * @return array
   */
  static function getValueRepresentation($vr_name) {
    return self::$value_representations[$vr_name];
  }
  
  /**
   * Get the characteristics of the given data set, identified by is group and is element number
   * 
   * @param integer $group	 The group number
   * 
   * @param integer $element The element number
   * 
   * @return array
   */
  static function getDataSet($group, $element) {
    return self::$data_sets[$group][$element];
  }
  
  /**
   * Check if the SOP class is supported by Mediboard
   * 
   * @param string $uid The UID of the SOP class
   * 
   * @return boolean
   */
  static function isSOPClassSupported($uid) {
    return array_key_exists($uid, self::$sop_classes);
  }
  
  /**
   * Check if the transfer syntax is supported by Mediboard
   * 
   * @param string $uid The UID of the transfer syntax
   * 
   * @return boolean
   */
  static function isTransferSyntaxSupported($uid) {
    return array_key_exists($uid, self::$transfer_syntaxes);
  }
}
?>