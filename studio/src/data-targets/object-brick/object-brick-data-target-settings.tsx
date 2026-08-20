import React from "react";
import { Input } from "antd";
import { Form, Switch } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";

export function ObjectBrickDataTargetSettings({
    settings,
    onChange,
}: DynamicTypeDataTargetRenderProps): React.JSX.Element {
    const s = settings.settings ?? {};

    return (
      <>
        <Form.Item
          label="Brick Container Field"
          required
        >
          <Input
            onChange={ (e) => onChange({ ...settings, settings: { ...s, fieldName: e.target.value } }) }
            value={ s.fieldName ?? "" }
          />
        </Form.Item>
        <Form.Item label="Remove Bricks Of Other Types">
          <Switch
            checked={ s.removeOtherTypes ?? false }
            onChange={ (checked) => onChange({ ...settings, settings: { ...s, removeOtherTypes: checked } }) }
            size="small"
          />
        </Form.Item>
      </>
    );
}
