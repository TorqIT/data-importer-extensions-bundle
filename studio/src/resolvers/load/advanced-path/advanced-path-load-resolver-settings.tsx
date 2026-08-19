import React from "react";
import { Input } from "antd";
import { Form, FormKit } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeResolverRenderProps } from "../../../common/types/DynamicTypeResolverRegistry";

export function AdvancedPathLoadResolverSettings(_props: DynamicTypeResolverRenderProps): React.JSX.Element {
    return (
      <FormKit.Panel>
        <Form.Item
          extra="Dynamic path expression built from input data"
          label="Advanced Path"
          name={ ["resolverConfig", "loadingStrategy", "settings", "advancedPath"] }
          required
          rules={ [{ required: true, message: "Advanced path is required." }] }
        >
          <Input />
        </Form.Item>
      </FormKit.Panel>
    );
}
