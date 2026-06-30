import React from "react";
import { Input } from "antd";
import { Form, FormKit } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeResolverRenderProps } from "../../../common/types/DynamicTypeResolverRegistry";

export function AdvancedParentCreateResolverSettings(_props: DynamicTypeResolverRenderProps): React.JSX.Element {
    return (
        <FormKit.Panel>
            <Form.Item
                name={["resolverConfig", "createLocationStrategy", "settings", "advancedParent"]}
                label="Advanced Parent Path"
                required
                rules={[{ required: true, message: "Advanced parent path is required." }]}
                extra="Dynamic path expression built from input data"
            >
                <Input />
            </Form.Item>
            <Form.Item
                name={["resolverConfig", "createLocationStrategy", "settings", "fallbackPath"]}
                label="Fallback Path"
                extra="Used if the computed parent path cannot be resolved"
            >
                <Input />
            </Form.Item>
        </FormKit.Panel>
    );
}
