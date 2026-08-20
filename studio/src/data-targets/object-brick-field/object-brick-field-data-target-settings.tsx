import React from "react";
import { Input } from "antd";
import { Form, Switch } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";

export function ObjectBrickFieldDataTargetSettings({
    settings,
    onChange,
}: DynamicTypeDataTargetRenderProps): React.JSX.Element {
    const s = settings.settings ?? {};
    const writeIfTargetIsNotEmpty = s.writeIfTargetIsNotEmpty ?? true;

    return (
      <>
        <Form.Item
          label="Brick Container Field"
          required
        >
          <Input
            onChange={ (e) => onChange({ ...settings, settings: { ...s, brickField: e.target.value } }) }
            value={ s.brickField ?? "" }
          />
        </Form.Item>
        <Form.Item
          label="Brick Attribute"
          required
        >
          <Input
            onChange={ (e) => onChange({ ...settings, settings: { ...s, fieldName: e.target.value } }) }
            value={ s.fieldName ?? "" }
          />
        </Form.Item>
        <Form.Item label="Write if Target is Not Empty">
          <Switch
            checked={ writeIfTargetIsNotEmpty }
            onChange={ (checked) =>
                        onChange({
                            ...settings,
                            settings: {
                                ...s,
                                writeIfTargetIsNotEmpty: checked,
                                writeIfSourceIsEmpty: checked ? s.writeIfSourceIsEmpty : false,
                            },
                        })
                    }
            size="small"
          />
        </Form.Item>
        <Form.Item label="Write if Source is Empty">
          <Switch
            checked={ s.writeIfSourceIsEmpty ?? false }
            disabled={ !writeIfTargetIsNotEmpty }
            onChange={ (checked) => onChange({ ...settings, settings: { ...s, writeIfSourceIsEmpty: checked } }) }
            size="small"
          />
        </Form.Item>
      </>
    );
}
