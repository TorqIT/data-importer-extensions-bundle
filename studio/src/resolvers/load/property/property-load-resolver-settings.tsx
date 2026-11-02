import React from "react";
import { Input } from "antd";
import { Form, FormKit } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeResolverRenderProps } from "../../../common/types/DynamicTypeResolverRegistry";

export function PropertyLoadResolverSettings(_props: DynamicTypeResolverRenderProps): React.JSX.Element {
    return (
      <FormKit.Panel>
        <Form.Item
          label="Property Name"
          name={ ["resolverConfig", "loadingStrategy", "settings", "propertyName"] }
          required
          rules={ [{ required: true, message: "Property name is required." }] }
        >
          <Input />
        </Form.Item>
        <Form.Item
          extra="Data source column containing the property value"
          label="Value Index"
          name={ ["resolverConfig", "loadingStrategy", "settings", "valueIndex"] }
        >
          <Input />
        </Form.Item>
      </FormKit.Panel>
    );
}
