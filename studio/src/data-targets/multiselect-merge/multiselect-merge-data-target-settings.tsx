import React from "react";
import { Form, Select } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";

export function MultiselectMergeDataTargetSettings({
    classFieldOptions,
    settings,
    onChange,
}: DynamicTypeDataTargetRenderProps): React.JSX.Element {
    const s = settings.settings ?? {};

    return (
      <Form.Item
        label="Field Name"
        required
      >
        <Select
          onChange={ (v) => onChange({ ...settings, settings: { ...s, fieldName: v } }) }
          options={ classFieldOptions }
          value={ s.fieldName }
        />
      </Form.Item>
    );
}
