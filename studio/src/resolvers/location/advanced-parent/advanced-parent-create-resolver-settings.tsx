import React from "react";
import { Input } from "antd";
import { Form, FormKit } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeResolverRenderProps } from "../../../common/types/DynamicTypeResolverRegistry";

export function AdvancedParentCreateResolverSettings(_props: DynamicTypeResolverRenderProps): React.JSX.Element {
    return (
      <FormKit.Panel>
        <Form.Item
          extra="Dynamic path expression built from input data"
          label="Advanced Parent Path"
          name={ ["resolverConfig", "createLocationStrategy", "settings", "advancedParent"] }
          required
          rules={ [{ required: true, message: "Advanced parent path is required." }] }
        >
          <Input />
        </Form.Item>
        <Form.Item
          extra="Used if the computed parent path cannot be resolved"
          label="Fallback Path"
          name={ ["resolverConfig", "createLocationStrategy", "settings", "fallbackPath"] }
        >
          <Input />
        </Form.Item>
      </FormKit.Panel>
    );
}
